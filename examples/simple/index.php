<!DOCTYPE html><?php
require '../../vendor/autoload.php';
use function WebComponentSSR\getClientTemplate;
use function WebComponentSSR\getTemplate;

WebComponentSSR\templateLocation(__DIR__ . '/js/templates');
?>

<!-- server rendered -->
<?= getTemplate('x-list', getTemplate('x-list-item', 'content')); ?>

<!-- client rendered -->
<x-list-item>
    <span>temp</span>
</x-list-item>

<!-- client template -->
<?= getClientTemplate('x-list'); ?>
<?= getClientTemplate('x-list-item'); ?>

<script async type="module" src="/js/main.js"></script>
