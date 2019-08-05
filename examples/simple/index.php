<!DOCTYPE html><?php
require '../../vendor/autoload.php';
use function Slogsdon\SSRWebComponents\getClientTemplate;
use function Slogsdon\SSRWebComponents\getTemplate;
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
