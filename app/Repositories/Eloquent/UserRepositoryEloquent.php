<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Repositories\Eloquent\BaseRepositoryEloquent;
use App\Repositories\Eloquent\Criteria\User\FilterCriteria;
use App\Repositories\Eloquent\Criteria\User\SortCriteria;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Validators\User\UserGenerateKeyValidation;
use App\Repositories\Validators\User\UserLoginValidation;
use App\Repositories\Validators\User\UserModifyValidation;
use App\Repositories\Validators\User\UserStoreValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepositoryEloquent extends BaseRepositoryEloquent implements UserRepository
{
    protected $userRoles = [
        "superAdmin" => [
            "superAdmin",
            "admin",
            "company",
            "customer",
        ],
        "admin" => [
            "admin",
            "company",
            "customer",
        ],
        "customer" => [
            "customer",
        ],
        "company" => [
            "company",
        ],
    ];

    /**
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function storeValidator(array $parameters)
    {
        return UserStoreValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @param User $user
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function modifyValidator(array $parameters, User $user)
    {
        $validator = new UserModifyValidation($parameters, $user);
        return $validator->createDefaultValidator();
    }

    /**
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function loginValidator(array $parameters)
    {
        return UserLoginValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function generateKeyValidator(array $parameters)
    {
        return UserGenerateKeyValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function prepareData(array $parameters): array
    {
        $user_info = Arr::only($parameters, [
            "phone",
            "tax_id",
            "address",
        ]);

        $data = [
            "name" => Arr::get($parameters, "name"),
            "email" => Arr::get($parameters, "email"),
            "password" => bcrypt(Arr::get($parameters, "password")),
            "user_role" => Arr::get($parameters, "user_role"),
            "user_info" => $user_info,
        ];

        return $data;
    }

    /**
     * @param array $parameters
     * @param User $user
     * @return array
     */
    protected function prepareModifyData(array $parameters, User $user): array
    {
        $update = [
            "name" => Arr::get($parameters, "name", $user->name),
            "email" => Arr::get($parameters, "email", $user->email),
            "user_role" => Arr::get($parameters, "user_role", $user->user_role),
            "user_info" => [
                "phone" => Arr::get($parameters, "phone", $user->user_info->phone),
                "tax_id" => Arr::get($parameters, "tax_id", $user->user_info->tax_id),
                "address" => Arr::get($parameters, "address", $user->user_info->address),
            ]
        ];

        if (!empty(Arr::get($parameters, "password"))) {
            $update["password"] = bcrypt(Arr::get($parameters, "password"));
        }

        return $update;
    }

    /**
     * @return Builder
     */
    protected function query()
    {
        $this->applyCriteria();
        $this->applyScope();
        $query = $this->model->newQuery();
        $this->resetCriteria();
        $this->resetScope();
        return $query;
    }

    /**
     * Fuction for searching and sorting
     *
     * @return Builder
     */
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(FilterCriteria::class);
        $this->pushCriteria(SortCriteria::class);
    }

    /**
     * @return User
     */
    public function model()
    {
        return User::class;
    }

    /**
     * Function for check permission role of user
     * @return Builder
     *
     */
    protected function roleCheck()
    {
        return $this->whereIn("user_role", Arr::get($this->userRoles, Auth::user()->user_role));
    }

    /**
     * Function for listing users
     *
     * @param array $parameters
     * @return \App\Models\User
     *
     */
    public function take(array $parameters = [])
    {
        $query = $this->roleCheck()->whereNot("id", Auth::user()->id);

        if (Arr::get($parameters, "per_page")) {
            $perPage = Arr::get($parameters, "per_page");
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    /**
     * Function for get user by id
     *
     * @param $id
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException : 100006 => "USER_NOT_FOUND"
     *
     */
    public function findById($id)
    {
        $query = $this->roleCheck();
        $user = $query->find($id);
        if (empty($user)) {
            throw new RepositoryException(100006);
        }

        return $user;
    }

    /**
     * Function for store new user
     *
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100000 => "USER_ERROR"
     * 100004 => "USER_CREATE_VALIDATE_ERROR"
     * 100005 => "USER_CREATE_ERROR"
     *
     */
    public function store(array $parameters)
    {
        if (empty(Arr::get($parameters, "register"))) {
            if ((!in_array(Auth::user()->user_role, ["admin", "superAdmin"])) ||
                (Auth::user()->user_role == "admin" && Arr::get($parameters, "user_role") == "superAdmin")
            ) {
                throw new RepositoryException(100000, ["You do not have permission"]);
            }
        } else {
            $parameters["user_role"] = "customer";
        }

        /** @var Validator $validator */

        $validator = $this->storeValidator($parameters);
        if ($validator->fails()) {
            throw new RepositoryException(100004, $validator->errors()->toArray());
        }
        try {
            $new_user = DB::transaction(function () use ($parameters) {
                $insert = $this->prepareData($parameters);
                $new_user = $this->query()->create($insert);
                return $new_user;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(100005, [$e->getMessage()]);
        }

        return $new_user;
    }

    /**
     * Function for modify user {id}
     *
     * @param $id
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100007 => "USER_UPDATE_VALIDATE_ERROR"
     * 100035 => "USER_UPDATE_ERROR"
     *
     */
    public function modify($id, array $parameters)
    {
        $user = $this->findById($id);

        /** @var Validator $validator */

        $validator = $this->modifyValidator($parameters, $user);

        if ($validator->fails()) {
            throw new RepositoryException(100007, $validator->errors()->toArray());
        }

        try {
            $new_user = DB::transaction(function () use ($parameters, $user) {
                $new_data = $this->prepareModifyData($parameters, $user);
                $user->update($new_data);
                return $user;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(100035, [$e->getMessage()]);
        }

        return $new_user;
    }

    /**
     * Function for modify user {id}
     *
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100000 => "USER_ERROR"
     * 100033 => "USER_DELETE_ERROR"
     *
     */
    public function destroy($id)
    {
        $user = $this->findById($id);
        if ((!in_array(Auth::user()->user_role, ["admin", "superAdmin"])) ||
            (Auth::user()->user_role == "admin" && $user->user_role == "superAdmin") ||
            (Auth::user()->id == $user->id)
        ) {
            throw new RepositoryException(100000, ["You do not have permission"]);
        }

        try {
            return $user->delete();
        } catch (\Exception $e) {
            throw new RepositoryException(100033, [$e->getMessage()]);
        }
    }

    /**
     * Function for login user
     *
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100019 => "USER_LOGIN_VALIDATE_ERROR"
     * 100006 => "USER_NOT_FOUND"
     *
     */
    public function login(array $parameters)
    {
        /** @var Validator $validator */

        $validator = $this->loginValidator($parameters);
        if ($validator->fails()) {
            throw new RepositoryException(100019, $validator->errors()->toArray());
        }
        if (Auth::attempt([
            "email" => Arr::get($parameters, "email"),
            "password" => Arr::get($parameters, "password")
        ])) {
            $user = Auth::user();
            $success["token"] = $user->createToken(Arr::get($parameters, "email"))->accessToken;
            $success["user"] = $user;
            return  $success;
        } else {
            throw new RepositoryException(100006);
        }
    }

    /**
     * Function for logout user
     *
     * @param array $parameters
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100000 => "USER_ERROR"
     *
     */
    public function logout(User $user)
    {
        if (Auth::check()) {
            $token = $user->token();
            return $token->revoke();
        } else {
            throw new RepositoryException(100000);
        }
    }

    /**
     * Function for company generate secret key
     *
     * @param array $parameters
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 100000 => "USER_ERROR"
     * 100019 => "USER_LOGIN_VALIDATE_ERROR"
     * 100006 => "USER_NOT_FOUND"
     * 100036 => "USER_GENERATE_SECRET_KEY_ERROR"
     * 100037 => 'USER_DELETE_SECRET_KEY_ERROR'
     *
     */
    public function generateKey(array $parameters)
    {
        if (!in_array(Auth::user()->user_role, ["admin", "superAdmin"])) {
            throw new RepositoryException(100000, ["You do not have permission"]);
        }

        $clientRepository = app()->make("Laravel\Passport\ClientRepository");

        /** @var Validator $validator */
        $validator = $this->generateKeyValidator($parameters);
        if ($validator->fails()) {
            throw new RepositoryException(100019, $validator->errors()->toArray());
        }

        $companyUser = $this->model->where("name", Arr::get($parameters, "name"))
            ->where("id", Arr::get($parameters, "company_id"))
            ->whereUserRole("company")
            ->first();

        if (empty($companyUser)) {
            throw new RepositoryException(100006);
        }

        try {
            if (!empty($companyUser->passport_id)) {
                $oldClient = $clientRepository->find($companyUser->passport_id);
                if (!$oldClient->delete()) {
                    throw new RepositoryException(100037);
                }
            }
            $client = $clientRepository->create(Arr::get($parameters, "company_id"), Arr::get($parameters, "name"), "", "users");
            $companyUser->passport_id = $client->id;
            if ($companyUser->save()) {
                return [
                    "uuid" => $client->id,
                    "secret" => $client->secret,
                ];
            } else {
                throw new RepositoryException(100036, ["Can not save passport id to user."]);
            }
        } catch (\Exception $e) {
            throw new RepositoryException(100036, [$e->getMessage()]);
        }
    }
}
