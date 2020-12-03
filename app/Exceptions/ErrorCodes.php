<?php

namespace App\Exceptions;

class ErrorCodes
{
    const ERROR_UNKNOWN = 0;
    const ERROR_BETSLIP_STRUCTURE_MISMATCHED = 1;
    const ERROR_MINIMUM_STAKE_AMOUNT = 2;
    const ERROR_MAXIMUM_STAKE_AMOUNT = 3;
    const ERROR_MINIMUM_NUMBER_OF_SELECTION = 4;
    const ERROR_MAXIMUM_NUMBER_OF_SELECTION = 5;
    const ERROR_SELECTION_MINIMUM_ODD = 6;
    const ERROR_SELECTION_MAXIMUM_ODD = 7;
    const ERROR_SELECTION_DUPLICATE = 8;
    const ERROR_MAXIMUM_SELECTION = 9;
    const ERROR_PREVIOUS_ACTION_NOT_FINISHED = 10;
    const ERROR_INSUFFICIENT_BALANCE = 11;

    /**
     * Returning Error Messages.
     *
     * @param string $errorCode
     */
    public function getErrorMessage($errorCode = ''): string
    {
        switch ($errorCode) {
            case self::ERROR_BETSLIP_STRUCTURE_MISMATCHED:
                return 'Betslip structure mismatch';
                break;

            case self::ERROR_MINIMUM_STAKE_AMOUNT:
                    return 'Minimum stake amount is 0.3';
                    break;

            case self::ERROR_MAXIMUM_STAKE_AMOUNT:
                return 'Maximum stake amount is 10000';
                break;

            case self::ERROR_MINIMUM_NUMBER_OF_SELECTION:
                return 'Minimum number of selections is 1';
                break;

            case self::ERROR_MAXIMUM_NUMBER_OF_SELECTION:
                return 'Maximum number of selections is 20';
                break;

            case self::ERROR_SELECTION_MINIMUM_ODD:
                return 'Minimum odds are 1';
                break;

            case self::ERROR_SELECTION_MAXIMUM_ODD:
                return 'Maximum odds are 10000';
                break;

            case self::ERROR_SELECTION_DUPLICATE:
                return 'Duplicate selection found';
                break;

            case self::ERROR_MAXIMUM_SELECTION:
                return 'Maximum win amount is 20000';
                break;

            case self::ERROR_PREVIOUS_ACTION_NOT_FINISHED:
                return 'Your previous action is not finished yet';
                break;

            case self::ERROR_INSUFFICIENT_BALANCE:
                return 'Insufficient balance';
                break;

            default:
                return 'Unknown error';
                break;
        }
    }
}
