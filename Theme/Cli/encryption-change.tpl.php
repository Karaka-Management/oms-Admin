<?php declare(strict_types=1);

echo 'Use "/admin/encryption/change -old {old_hash} -new {new_hash}" to change the encryption'
    , "\n\n"
    , 'This is very slow and can take a long time because all the content that is encrypted with the old key will be decrypted and then again encrypted with the new key.';
