<!DOCTYPE html><?php require '../vendor/autoload.php'; ?>

<!-- server rendered -->
<?= getTemplate('x-list-item', ['content' => 'test']); ?>

<!-- client rendered -->
<!-- <x-list-item>
    <span slot="content">temp</span>
</x-list-item> -->

<!-- client template -->
<?= getClientTemplate('x-list-item', ['content']); ?>

<script type="module" src="/js/main.js"></script>
