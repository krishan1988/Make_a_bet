<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorCodes;
use App\Models\BalanceTransaction;
use App\Models\Bet;
use App\Models\BetSelections;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class BetController extends Controller
{
    /**
     * Creating Bet function.
     *
     * @return void
     */
    public function create(Request $request)
    {
        $requestedData = $request->all();

        //Validating request
        $isValidate = $this->validateRequest($requestedData);
        if (isset($isValidate['status']) && $isValidate['status'] == false) {
            return response()->json($isValidate['errors'], 400);
        }

        //Checking Player Exists or not
        $playerData = Player::find($requestedData['player_id']);

        if ($playerData === null) {
            // Create Player From given ID
            $player = new Player();
            $player->id = $requestedData['player_id'];
            $player->balance = env('DEFAULT_BALANCE');
            $player->save();

            //Create Default Balance
            $balance = new BalanceTransaction();
            $balance->player_id = $requestedData['player_id'];
            $balance->amount = env('DEFAULT_BALANCE');
            $balance->amount_before = 0;

            $balance->save();
        }

        //Create Bet Function
        $betProcess = $this->bettingProcess($requestedData);

        if ($betProcess === true) {
            return response()->json([], 201);
        }
    }

    /**
     * Validate Request function.
     *
     * @param [type] $requestedData
     */
    private function validateRequest($requestedData): array
    {
        $messages = [
            'stake_amount.min' => ErrorCodes::ERROR_MINIMUM_STAKE_AMOUNT,
            'stake_amount.max' => ErrorCodes::ERROR_MAXIMUM_STAKE_AMOUNT,
            'selections.*.id.distinct' => ErrorCodes::ERROR_SELECTION_DUPLICATE,
            'selections.min' => ErrorCodes::ERROR_MINIMUM_NUMBER_OF_SELECTION,
            'selections.max' => ErrorCodes::ERROR_MAXIMUM_NUMBER_OF_SELECTION,
            'stake_amount.max_win_amount' => ErrorCodes::ERROR_MAXIMUM_SELECTION,
            'stake_amount.check_balance' => ErrorCodes::ERROR_INSUFFICIENT_BALANCE,
        ];

        Validator::extend('max_win_amount', function ($attribute, $value, $parameters, $validator) {
            $requestedData = $validator->getData();

            $totalOdd = 1;
            if (isset($requestedData['selections']) && isset($requestedData['stake_amount'])) {
                foreach ($requestedData['selections'] as $selectionOdds) {
                    $totalOdd = $totalOdd * $selectionOdds['odds'];
                }

                $totalWinAmount = $requestedData['stake_amount'] * $totalOdd;

                if ($totalWinAmount < 20000) {
                    return true;
                }
            }

            return false;
        });

        Validator::extend('check_balance', function ($attribute, $value, $parameters, $validator) {
            $requestedData = $validator->getData();

            if (isset($requestedData['player_id']) && $requestedData['stake_amount']) {
                $playerData = Player::find($requestedData['player_id']);

                if ($playerData != null) {
                    if ($playerData->balance <= $requestedData['stake_amount']) {
                        return false;
                    }
                }
            }

            return true;
        });

        $validator = Validator::make($requestedData, [
            'player_id' => 'required|integer',
            'stake_amount' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|min:0.3|max:10000|max_win_amount|check_balance',
            'selections' => 'required|array|min:1|max:20',
            'selections.*.id' => 'required|integer|distinct:selections.*.id',
            'selections.*.odds' => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorsArray = [];
            $selectionErrorArray = [];
            foreach ($errors->all() as $Errorcode) {
                $errorMessage = new ErrorCodes();

                if ($Errorcode == ErrorCodes::ERROR_SELECTION_DUPLICATE) {
                    // getDuplicate Values
                    $collection = collect($requestedData['selections']);
                    $groupedByValue = $collection->groupBy('id');

                    $duplicateValues = $groupedByValue->filter(function (Collection $groups) {
                        return $groups->count() > 1;
                    });

                    foreach ($duplicateValues as $duplicate => $key) {
                        $selectionError['id'] = $duplicate;
                        $selectionError['errors'] = [];

                        $error['code'] = $Errorcode;
                        $error['message'] = $errorMessage->getErrorMessage($Errorcode);
                        array_push($selectionError['errors'], $error);
                        array_push($selectionErrorArray, $selectionError);
                    }
                } else {
                    $error['code'] = $Errorcode;
                    if ($errorMessage->getErrorMessage($Errorcode) == 'Unknown error') {
                        $error['code'] = 0;
                    }
                    $error['message'] = $errorMessage->getErrorMessage($Errorcode);
                    array_push($errorsArray, $error);
                }
            }
            $status['status'] = false;
            $status['errors'] = ['errors' => $errorsArray, 'selections' => $selectionErrorArray];

            return $status;
        }
        $staus['status'] = true;

        return $staus;
    }

    /**
     * Betting Process function.
     *
     * @param [type] $requestData
     */
    private function bettingProcess($requestData): bool
    {
        $bet = new Bet();
        $bet->stake_amount = $requestData['stake_amount'];

        if ($bet->save()) {
            foreach ($requestData['selections'] as $selection) {
                $betSelections = new BetSelections();
                $betSelections->bet_id = $bet->id;
                $betSelections->selection_id = $selection['id'];
                $betSelections->odds = $selection['odds'];
                $betSelections->save();
            }

            //Update Balance Transaction
            $currentBalanceData = BalanceTransaction::where('player_id', $requestData['player_id'])->first();
            $beforeAmount = $currentBalanceData->amount;
            $currentBalanceData->amount_before = $currentBalanceData->amount;
            $currentBalanceData->amount = $beforeAmount - $requestData['stake_amount'];
            $currentBalanceData->save();

            //Update Player Current Balance
            $player = Player::where('id', $requestData['player_id'])->first();
            $player->balance = $beforeAmount - $requestData['stake_amount'];
            $player->save();

            return true;
        }

        return false;
    }
}
