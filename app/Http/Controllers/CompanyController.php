<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(Gate::denies('viewAny', Company::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
            $query = Company::with('owner')->mostRecent(); //Deja acceder a los datos del propietario mediante el modelo OWNER

            /* Para acceder a los campos del filtro */
            $filters = $request->only(['name', 'city', 'country']);


            $companies = $query->filter($filters)->paginate(10)->withQueryString();

        return inertia(
            'Company/IndexCompany',
            [
                'companies' => $companies,
                'filters' => $filters,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Gate::denies('create', Company::class))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }
        return inertia('Company/CreateCompany');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
        $validated = $request->validate([
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|string|email',
        'address' => 'required|string|max:100',
        'city' => 'required|string|max:50',
        'country' => 'required|string|max:50',
        'phone' => 'required|string|max:20',
        'web_address' => 'required|url',
        'tax_id' => 'required|string|max:20',
    ]);

    $validated['company_code'] = Company::invitationCode(); //Genera el código

    $company = $request->user()->companyOwner()->create($validated); //Crea el registro y trae el id del usuario de la función del modelo user
    $company->member()->attach($request->user()->id, [
        'role' => 'propietario' //cambia el rol a propietario por crear la empresa
    ]);

    $user = Auth::user(); //cambio el rol del usuario a gerente de proyectos
    if($user->hasRole('user') && !$user->hasRole('manager'))
        {
            $user->removeRole('user');
            $user->assignRole('manager');
        }

    //los datos se guardan en BD
    return redirect()->route('companies.show', $company->id)->with('success', 'Empresa creada con éxito.');
    }
    catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al generar empresa.')->withInput();//withInput mantiene los datos del form
        }
}

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
        if(Gate::denies('view', $company))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        $user = Auth::user();

        return inertia('Company/ShowCompany', [
            'company' => $company->load('member'),
            'can' => [
                'delete' => $user->can('update', $company),
                'update' => $user->can('delete', $company),
        ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
        if(Gate::denies('update', $company))
            {
                abort(403, 'No tienes los permisos necesarios para ver esta pagina.');
            }

        return inertia(
            'Company/EditCompany',
        [
            'company' => $company
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        try{
            $validated = $request->validate([
                        'name' => 'required|string|min:3|max:100',
                        'email' => 'required|string|email',
                        'address' => 'required|string|max:100',
                        'city' => 'required|string|max:50',
                        'country' => 'required|string|max:50',
                        'phone' => 'required|string|max:20',
                        'web_address' => 'required|url',
                        'tax_id' => 'required|string|max:20',
                    ]);
            $validated['company_code'] = Company::invitationCode(); //Genera el código
            $company->update($validated);

            //los datos se guardan en BD
            return redirect()->route('companies.redirect')->with('success', 'Empresa actualizada.');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al actualizar empresa.')->withInput();//withInput mantiene los datos del form
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if(Gate::denies('delete', $company))
            {
                abort(403, 'No tienes los permisos necesarios para realizar esta acción.');
            }

        $company->delete();

        $user = Auth::user(); //revoca el permiso de gerente de proyectos y lo devuelve a usuario
        $user->removeRole('manager');
        $user->assignRole('user');

        return redirect()->route('companies.index')->with('success', 'Empresa eliminada exitosamente.');
    }

    public function joinCompany()
    {
        if(Gate::denies('joinCompany', Company::class))
            {
                return redirect()->route('inicio')->with('error', 'No puedes unirte a esta empresa.');
            }
        return inertia('Company/JoinCompany');
    }

    public function checkCode(Request $request)
    {
        try{
            $validated = $request->validate([
                'code' => 'required|string|exists:companies,company_code'
            ]);

            $company = Company::where('company_code', $validated['code'])->firstOrFail(); //busca la empresa por el código

            if($company->member()->where('user_id', $request->user()->id)->exists())//verifica si el usuario ya pertenece a la empresa
                {
                    return redirect()->back()->with('error', 'Ya eres miembro de la empresa proporcionada');
                }

            //Deja que el usuario pueda unirse a la empresa
            $company->member()->attach(Auth::user()->id, ['role' => 'miembro', 'joined_at' => now()]);
            return redirect()->route('companies.show', $company->id)->with('success', 'Te has unido a, ' . $company->name . ' exitosamente');
        }catch(ValidationException $e){
            throw $e;
        }catch(\Exception $e){
            //si los datos no se guardan, se muestra mensaje de error
            return redirect()->back()->with('error', 'Error al ingresar código.')->withInput();//withInput mantiene los datos del form
        }
    }

    public function redirectTo()
    {
        $user = Auth::user();

        if($user->hasRole('admin')){ //El usuario ADMIN puede ver todas las empresa
            return redirect()->route('companies.index');
        }elseif($user->companies()->exists()){ //Cualquier otro usuario sea miembro o propietario puede ver la info de esa empresa en especifico
            $company = $user->companies()->first();
            return redirect()->route('companies.show', $company->id);
        }else{ //Si el usuario no pertenece a ninguna empresa no podrá ver nada
            return redirect()->route('companies.index');
        }
    }

    public function listMember(Company $company)
    {
        if(Gate::denies('viewList', $company))
            {
                return redirect()->route('companies.show', $company->id)->with('error', 'No tienes permiso para ver esta empresa.');
            }

        $members = $company->member()->get();

        return inertia('Company/MemberListCompany', [
            'company' => $company,
            'members' => $members,
        ]);
    }

    public function leaveCompany(Company $company, User $user){
        if(Gate::denies('leaveCompany', $company))
            {
                return redirect()->route('companies.show', $company->id)->with('error', 'No tienes permiso para salir de esta empresa.');
            }

        if($user->isOwner($company)){
            return redirect()->route('companies.show', $company->id)->with('error', 'No puedes salir de tu propia empresa.');
        }
        $company->member()->detach($user->id);
        return redirect()->route('companies.listMember', $company->id)->with('success', 'Has sacado al usuario de la empresa exitosamente.');
    }
}
