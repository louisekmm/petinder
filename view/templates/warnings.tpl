<?php

if ($this->session->isMessage()) {
	$msg = $this->session->getMessage();
	if ($msg[0] == 1) $alertatipo = 'alert-success';
	else $alertatipo = 'alert-danger';
	?>
	<div class="alert <?=$alertatipo?>">
		<b><?=$msg[1];?></b>
	</div>

	<?php
}