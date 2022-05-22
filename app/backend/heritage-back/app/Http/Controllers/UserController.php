<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // On récupère tous les utilisateurs
        $users = User::paginate(10);

        // On retourne les informations des utilisateurs en JSON
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        // La validation de données
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        // On crée un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        // On retourne les informations du nouvel utilisateur en JSON
        return response()->json($user, 201);
    }

    public function me() {
        $auth = app('firebase.auth');
        // On retourne les informations de l'utilisateur en JSON
        // return new UserResource($user);
        // $idTokenString = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjZhNGY4N2ZmNWQ5M2ZhNmVhMDNlNWM2ZTg4ZWVhMGFjZDJhMjMyYTkiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vaGVyaXRhZ2UtYmUiLCJhdWQiOiJoZXJpdGFnZS1iZSIsImF1dGhfdGltZSI6MTY0OTQ0OTUwMSwidXNlcl9pZCI6IkJ3S1I0N3hCcm9SWjRiTkxDNU9QaFE4RU40RjMiLCJzdWIiOiJCd0tSNDd4QnJvUlo0Yk5MQzVPUGhROEVONEYzIiwiaWF0IjoxNjQ5NDUzODA4LCJleHAiOjE2NDk0NTc0MDgsImVtYWlsIjoiZGF2Z2lsc29uQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJmaXJlYmFzZSI6eyJpZGVudGl0aWVzIjp7ImVtYWlsIjpbImRhdmdpbHNvbkBnbWFpbC5jb20iXX0sInNpZ25faW5fcHJvdmlkZXIiOiJwYXNzd29yZCJ9fQ.G7ny1338a61lWmAIZyJP1VH36bHyMvgNMaqXl_pc1itGEfvpfzhqHtR3-pBgLJmzl3MLQaA_v0aXIdjOwoqyCUKB8BLPyC4ZnVnSJvYxSYjbS264M2JTqKg25MTSC95ETpaaVs9YxzwOUDLUTszy6ZibDmEgAn-DC50_l3DMzFwNDODTQSr6Pv2UFQxXhtkjrUUedqY4107GASkg5VQ-TgRYKhuXzhLhcYv8mbMpK_8hKAeNb5c7OVv-O1zgUdbp84snmAHn4ZxkozIG3uWgtBuARZWcmYBXNSC2eE3uaTnSrGa2T3PuecbUn3-FDOZ0hSeupronfC_99w5RCrOsKA';
        $idTokenString = str_replace('bearer ', '', \Request::header('Authorization'));

        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);

            $uid = $verifiedIdToken->claims()->get('sub');

            $user = $auth->getUser($uid);
            return response()->json($user, 200);
        } catch (FailedToVerifyToken $e) {
            echo 'The token is invalid: '.$e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        var_dump($user);
        $auth = app('firebase.auth');
        // On retourne les informations de l'utilisateur en JSON
        // return new UserResource($user);
        // $idTokenString = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjZhNGY4N2ZmNWQ5M2ZhNmVhMDNlNWM2ZTg4ZWVhMGFjZDJhMjMyYTkiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vaGVyaXRhZ2UtYmUiLCJhdWQiOiJoZXJpdGFnZS1iZSIsImF1dGhfdGltZSI6MTY0OTQ0OTUwMSwidXNlcl9pZCI6IkJ3S1I0N3hCcm9SWjRiTkxDNU9QaFE4RU40RjMiLCJzdWIiOiJCd0tSNDd4QnJvUlo0Yk5MQzVPUGhROEVONEYzIiwiaWF0IjoxNjQ5NDUzODA4LCJleHAiOjE2NDk0NTc0MDgsImVtYWlsIjoiZGF2Z2lsc29uQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJmaXJlYmFzZSI6eyJpZGVudGl0aWVzIjp7ImVtYWlsIjpbImRhdmdpbHNvbkBnbWFpbC5jb20iXX0sInNpZ25faW5fcHJvdmlkZXIiOiJwYXNzd29yZCJ9fQ.G7ny1338a61lWmAIZyJP1VH36bHyMvgNMaqXl_pc1itGEfvpfzhqHtR3-pBgLJmzl3MLQaA_v0aXIdjOwoqyCUKB8BLPyC4ZnVnSJvYxSYjbS264M2JTqKg25MTSC95ETpaaVs9YxzwOUDLUTszy6ZibDmEgAn-DC50_l3DMzFwNDODTQSr6Pv2UFQxXhtkjrUUedqY4107GASkg5VQ-TgRYKhuXzhLhcYv8mbMpK_8hKAeNb5c7OVv-O1zgUdbp84snmAHn4ZxkozIG3uWgtBuARZWcmYBXNSC2eE3uaTnSrGa2T3PuecbUn3-FDOZ0hSeupronfC_99w5RCrOsKA';
        $idTokenString = str_replace('bearer ', '', \Request::header('Authorization'));

        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);

            $uid = $verifiedIdToken->claims()->get('sub');

            $user = $auth->getUser($uid);
            return response()->json($user, 200);
        } catch (FailedToVerifyToken $e) {
            echo 'The token is invalid: '.$e->getMessage();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserStoreRequest $request, User $user)
    {
        // La validation de données
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // On modifie les informations de l'utilisateur
        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);

        // On retourne la réponse JSON
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // On supprime l'utilisateur
        $user->delete();

        // On retourne la réponse JSON
        return response()->json();
    }
}
