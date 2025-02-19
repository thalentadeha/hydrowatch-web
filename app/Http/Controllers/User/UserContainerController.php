<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Firestore;

class UserContainerController extends Controller
{
    protected $auth;
    protected $db;
    protected $authController;

    public function __construct(
        Firestore $firestore,
        Auth $auth,
        AuthController $authController
    ) {
        $this->auth = $auth;
        $this->db = $firestore->database();
        $this->authController = $authController;
    }

    public function index(Request $request)
    {
        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);
        $userData = $this->db->collection('user')->document($uid)->snapshot()->data();

        $containerQuery = $this->db->collection('container')->where('userID', '=', $uid);
        $containerDocs = $containerQuery->documents();

        $containerList = [];
        foreach ($containerDocs as $containerData) {
            if ($containerData->exists()) {
                $containerList[$containerData->id()] = $containerData->data();
            }
        }

        return view('user.container', [
            'userData' => $userData,
            'containerList' => $containerList,
        ]);
    }

    public function addContainer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nfc_id1' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id2' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id3' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id4' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'containerDesc' => 'nullable|string'
        ], [
            'nfc_id1.required' => 'NFC id should not be empty.',
            'nfc_id2.required' => 'NFC id should not be empty.',
            'nfc_id3.required' => 'NFC id should not be empty.',
            'nfc_id4.required' => 'NFC id should not be empty.',
            'nfc_id1.size' => 'NFC id should be 8 character.',
            'nfc_id2.size' => 'NFC id should be 8 character.',
            'nfc_id3.size' => 'NFC id should be 8 character.',
            'nfc_id4.size' => 'NFC id should be 8 character.',
            'nfc_id1.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id2.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id3.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id4.regex' => 'NFC id should be only consist A-F or 0-9.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            $containerID = $validated['nfc_id1'] .
                ":" . $validated['nfc_id2'] .
                ":" . $validated['nfc_id3'] .
                ":" . $validated['nfc_id4'];

            $containerList = $this->db->collection('container')->documents();

            $containerAvail = false;
            foreach ($containerList as $containerDoc) {
                if ($containerDoc->id() === strtoupper($containerID)) {
                    $containerAvail = true;
                    continue;
                }
            }

            if ($containerAvail) {
                return response()->json(['errors' => ['Container ID already taken.']], 422);
            }

            $containerData = [
                'userID' => $uid,
                'description' => $validated['containerDesc'] === null ? "" : $validated['containerDesc'],
            ];

            $this->db->collection('container')->document(strtoupper($containerID))->set($containerData);

            return response()->json(['success' => 'Add container successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }
    public function deleteContainer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nfc_id1' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id2' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id3' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
            'nfc_id4' => ['required', 'string', 'size:2', 'regex:/[A-Fa-f0-9]+$/'],
        ], [
            'nfc_id1.required' => 'NFC id should not be empty.',
            'nfc_id2.required' => 'NFC id should not be empty.',
            'nfc_id3.required' => 'NFC id should not be empty.',
            'nfc_id4.required' => 'NFC id should not be empty.',
            'nfc_id1.size' => 'NFC id should be 8 character.',
            'nfc_id2.size' => 'NFC id should be 8 character.',
            'nfc_id3.size' => 'NFC id should be 8 character.',
            'nfc_id4.size' => 'NFC id should be 8 character.',
            'nfc_id1.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id2.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id3.regex' => 'NFC id should be only consist A-F or 0-9.',
            'nfc_id4.regex' => 'NFC id should be only consist A-F or 0-9.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        $idToken = session('idToken');

        $uid = $this->authController->getUID($idToken);

        try {
            $validated = $validator->validate();

            $containerID = $validated['nfc_id1'] .
                ":" . $validated['nfc_id2'] .
                ":" . $validated['nfc_id3'] .
                ":" . $validated['nfc_id4'];

            $containerDoc = $this->db->collection('container')->document(strtoupper($containerID));
            if (
                !$containerDoc->snapshot()->exists() ||
                $containerDoc->snapshot()->data()['userID'] !== $uid
            ) {
                return response()->json(['errors' => ['Container ID not found.']], 422);
            }

            $containerDoc->delete();

            return response()->json(['success' => 'Delete container successful']);
        } catch (Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }
}
