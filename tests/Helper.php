<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Karaka
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Admin\tests;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\AccountMapper;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Utils\RnG\Email;
use phpOMS\Utils\RnG\Text;

class Helper {
    public static function createAccounts(int $n = 10)
    {
        $LOREM = \array_slice(Text::LOREM_IPSUM, 0, 25);
        $LOREM_COUNT = \count($LOREM) - 1;

        for ($i = 0; $i < $n; ++$i) {
            $account = new Account();

            $account->login = Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)];
            $account->name1 = \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);
            $account->name2 = \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);
            $account->name3 = \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);
            $account->tries = 0;
            $account->setEmail(Email::generateEmail());
            $account->setStatus(AccountStatus::ACTIVE);
            $account->setType(AccountType::USER);

            AccountMapper::create()->execute($account);
        }
    }
}