<?php

namespace Webkul\User\Http\Controllers;

use Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Webkul\User\Http\Requests\UserForm;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\AdminRepository;
use Illuminate\Support\Facades\DB;
use Session;

class UserController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * AdminRepository object
     *
     * @var \Webkul\User\Repositories\AdminRepository
     */
    protected $adminRepository;

    /**
     * RoleRepository object
     *
     * @var \Webkul\User\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\AdminRepository  $adminRepository
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(
        AdminRepository $adminRepository,
        RoleRepository $roleRepository
    )
    {
        $this->adminRepository = $adminRepository;

        $this->roleRepository = $roleRepository;

        $this->_config = request('_config');

        $this->middleware('guest', ['except' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = DB::table('admins')
            ->leftJoin('roles', 'admins.role_id', '=', 'roles.id')
            ->leftJoin('inventory_sources', 'admins.inventory_id', '=', 'inventory_sources.id')
            ->addSelect('admins.id as user_id', 'admins.name as user_name', 'admins.status', 'admins.email', 'roles.name as role_name','inventory_sources.name as inventory_name','admins.inventory_id as inventory_id');
        if( Session::get('inventory') != 0){
            $query = $query->where('admins.inventory_id','=',Session::get('inventory'));
        };
        $query = $query->orderBy('admins.id', 'desc');
        $users_list=$query->get()->toArray();
        $inventory_list = DB::table('inventory_sources')
        ->orderBy('id', 'asc')
        ->get()->toArray();
        return view($this->_config['view'], compact('users_list','inventory_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = $this->roleRepository->all();
        $inventory_list = DB::table('inventory_sources')
        ->orderBy('id', 'asc')
        ->get()->toArray();

        return view($this->_config['view'], compact('roles','inventory_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Webkul\User\Http\Requests\UserForm  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserForm $request)
    {
        $data = $request->all();

        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
            $data['api_token'] = Str::random(80);
        }

        Event::dispatch('user.admin.create.before');

        $admin = $this->adminRepository->create($data);

        Event::dispatch('user.admin.create.after', $admin);

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'User']));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = $this->adminRepository->findOrFail($id);

        $roles = $this->roleRepository->all();
        $inventory_list = DB::table('inventory_sources')
        ->where('id', '!=', '1')
        ->orderBy('id', 'desc')
        ->get()->toArray();

        return view($this->_config['view'], compact('user', 'roles','inventory_list'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Webkul\User\Http\Requests\UserForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserForm $request, $id)
    {
        $isPasswordChanged = false;

        $data = $request->all();

        if (! $data['password']) {
            unset($data['password']);
        } else {
            $isPasswordChanged = true;
            $data['password'] = bcrypt($data['password']);
        }

        if (isset($data['status'])) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        Event::dispatch('user.admin.update.before', $id);

        $admin = $this->adminRepository->update($data, $id);

        if ($isPasswordChanged) {
            Event::dispatch('user.admin.update-password', $admin);
        }

        Event::dispatch('user.admin.update.after', $admin);

        session()->flash('success', trans('admin::app.response.update-success', ['name' => 'User']));

        return redirect()->route($this->_config['redirect']);
    }

    public function quickUpdate()
    {
        $status = request()->status;
        $inventory_id = request()->inventory_id;
        $user_id = request()->selected_user;
        $productExchangeNote =  DB::table('admins')
            ->where('id', $user_id)
            ->update(['status' => $status,'inventory_id' => $inventory_id]);
        return response()->json(
            [
                'success' => True,
            ]
        );
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function destroy()
    {
        $id = request()->input('id');
        $user = $this->adminRepository->findOrFail($id);
        if ($this->adminRepository->count() == 1) {
            session()->flash('error', trans('admin::app.response.last-delete-error', ['name' => 'Admin']));
        } else {
            Event::dispatch('user.admin.delete.before', $id);

            if (auth()->guard('admin')->user()->id == $id) {
                return response()->json([
                    'redirect' => route('super.users.confirm', ['id' => $id]),
                ]);
            }

            try {
                $this->adminRepository->delete($id);

                session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Admin']));

                Event::dispatch('user.admin.delete.after', $id);

                return redirect()->route('admin.users.index');
            } catch (Exception $e) {
                session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Admin']));
            }
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Show the form for confirming the user password.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function confirm($id)
    {
        $user = $this->adminRepository->findOrFail($id);

        return view($this->_config['view'], compact('user'));
    }

    /**
     * destroy current after confirming
     *
     * @return \Illuminate\Http\Response
     */
    public function destroySelf()
    {
        $password = request()->input('password');

        if (Hash::check($password, auth()->guard('admin')->user()->password)) {
            if ($this->adminRepository->count() == 1) {
                session()->flash('error', trans('admin::app.users.users.delete-last'));
            } else {
                $id = auth()->guard('admin')->user()->id;

                Event::dispatch('user.admin.delete.before', $id);

                $this->adminRepository->delete($id);

                Event::dispatch('user.admin.delete.after', $id);

                session()->flash('success', trans('admin::app.users.users.delete-success'));

                return redirect()->route('admin.session.create');
            }
        } else {
            session()->flash('warning', trans('admin::app.users.users.incorrect-password'));

            return redirect()->route($this->_config['redirect']);
        }
    }
}
