<script src="<?php echo asset('js/directives.js'); ?>"></script>
<script src="<?php echo asset('js/filters.js'); ?>"></script>
<script src="<?php echo asset('js/services.js'); ?>"></script>
<script src="<?php echo asset('js/configs.js'); ?>"></script>
<script src="<?php echo asset('js/controllers/login.js'); ?>"></script>
<script src="<?php echo asset('js/controllers/account.js'); ?>"></script>
<script>
    angular.module("OxoAwards").constant("CSRF_TOKEN", '<?php echo csrf_token(); ?>');
</script>