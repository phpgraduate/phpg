<h1><?php echo $this->translate('A 403 error occurred') ?></h1>
<h2><?php echo $this->message ?></h2>

<?php if (isset($this->reason) && $this->reason): ?>

<?php
$reasonMessage= '';
switch ($this->reason) {
    case 'error-unauthorized':
        $reasonMessage = $this->translate('The requested controller was unable to dispatch the request. Probably You do not have access to this page.');
        break;
    default:
        $reasonMessage = $this->translate('We cannot determine at this time why a 403 was generated.');
        break;
}
?>

<p><?php echo $reasonMessage ?></p>

<?php endif ?>

<?php if (isset($this->exception) && $this->exception): ?>

<h2><?php echo $this->translate('Exception') ?>:</h2>

<p><b><?php echo $this->escapeHtml($this->exception->getMessage()) ?></b></p>

<h3><?php echo $this->translate('Stack trace') ?>:</h3>

<pre>
<?php echo $this->exception->getTraceAsString() ?>
</pre>

<?php endif ?>
