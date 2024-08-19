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
            'nfc_id1' => 'required|string|max:2',
            'nfc_id2' => 'required|string|max:2',
            'nfc_id3' => 'required|string|max:2',
            'nfc_id4' => 'required|string|max:2',
            'weight' => 'required|numeric',
            'volume' => 'required|numeric|min:100|max:6000',
            'containerDesc' => 'nullable|string'
        ], [
            'nfc_id1.required' => 'NFC id should not be empty.',
            'nfc_id2.required' => 'NFC id should not be empty.',
            'nfc_id3.required' => 'NFC id should not be empty.',
            'nfc_id4.required' => 'NFC id should not be empty.',
            'weight.required' => 'Weight should not be empty.',
            'volume.required' => 'Volume should not be empty.',
            'volume.min' => 'mL should not be less than 100.',
            'volume.max' => 'mL should not be more than 6000.',
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

            $containerDoc = $this->db->collection('container');
            $containerList = $containerDoc->documents();

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
                'volume' => $validated['volume'],
                'weight' => $validated['weight'],
                'description' => $validated['containerDesc'],
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
            'nfc_id1' => 'required|string|max:2',
            'nfc_id2' => 'required|string|max:2',
            'nfc_id3' => 'required|string|max:2',
            'nfc_id4' => 'required|string|max:2',
        ], [
            'nfc_id1.required' => 'NFC id should not be empty.',
            'nfc_id2.required' => 'NFC id should not be empty.',
            'nfc_id3.required' => 'NFC id should not be empty.',
            'nfc_id4.required' => 'NFC id should not be empty.',
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
