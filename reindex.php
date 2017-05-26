<?php
$command = 'php bin/magento indexer:reindex';

passthru($command);

$command = 'php bin/magento indexer:info';

passthru($command);


echo 'Done';