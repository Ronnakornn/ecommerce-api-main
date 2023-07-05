<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Token;
use Laravel\Passport\Passport;


class AuthController extends ApiBaseController
{
    protected $userRepository;
    protected $resource = UserResource::class;

    public function  __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register user customer only
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function register(Request $request)
    {
        $input = $request->all();
        $input['register'] = true;
        try {
            $new_user = $this->userRepository->store($input);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($new_user);
    }

    /**
     * Function for login user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function login(Request $request)
    {
        try {
            $user = $this->userRepository->login($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($user);
    }

    /**
     * Function for logout user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function logout(Request $request)
    {
        try {
            $this->userRepository->logout($request->user());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse([], null, null, false, "User is logout success");
    }

    /**
     * Function for company generate secret key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function generateKey(Request $request)
    {
        try {
            $result = $this->userRepository->generateKey($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($result, null, null, false, "Generate secret key success");
    }


    public function getToken(Request $request)
    {
        $accessToken = $request->bearerToken();

        // dd($accessToken);

        $token = Token::where('id', $accessToken)->first();
        dd($token);
        if ($token) {
            $clientId = $token->client_id;
            $client = Passport::client()->find($clientId);

            // dd($client);

            // $client now contains the client associated with the access token
        } else {
            // Access token not found or invalid
        }



        // $response = Http::asForm()->post('nginx/oauth/token', [
        //     'grant_type' => 'client_credentials',
        //     'client_id' => '998e89c5-0cf1-492b-ad52-a13375b0c9fa',
        //     'client_secret' => 'Z1rOH9nOKYOpghKMV24DPmmt3COnYky1noXUGEdb',
        //     'scope' => 'client',
        // ]);




        // dd($response);
        // $client = DB::table('oauth_access_tokens')
        // ->where('id', '640ca79c16ac10148b5e2b82f2880d228a947ca3cbb5224914b28f30f796c991cad36f2a5c1c9dd6')
        // ->first();

        // dd($client);

        // return  $this->successResponse(json_encode($client, 1), null, null, false, "");



        // Replace these with your actual credentials
        // $clientId = '998e9851-2958-405f-9f66-2c401059b7f7';
        // $clientSecret = 'ydsAOlPQXSQE7VAF9EyOr7wj7vVB8IGEvUl1ZwKi';

        // $response = Http::withHeaders([
        //     'Accept' => 'application/json',
        // ])->post('http://192.168.0.23/oauth/token', [
        //     'grant_type' => 'client_credentials',
        //     'client_id' => $clientId,
        //     'client_secret' => $clientSecret,
        // ]);

        // dd($response);

        // // Check if the request was successful
        // if ($response->successful()) {
        //     $accessToken = $response->json()['access_token'];

        //     // Now you can use the access token to make authenticated requests on behalf of the user
        //     $userDataResponse = Http::withToken($accessToken)->get('https://ecommerce-api.test/api/frontend/users');
        //     $userData = $userDataResponse->json();
        //     return $userData;

        //     // Do something with $userData, which should contain user information
        // } else {
        //     // Handle the case when the request was not successful
        //     $errorResponse = $response->json();
        //     // Handle the error response as needed
        //     return $errorResponse;
        // }
    }
}
