<?php
/*********************************************************************
*Moddified sql query function with error reporting                  * 
********************************************************************/
echo '
		</div>
	</div>
		<div id="footer">
			<div class="padding">
				<font color="white"><strong></strong></font> '.date('Y').'  &copy; <font color="white"><a href="'.$BASEURL.'" target="_self"><strong>'.$SITENAME.'</strong></a></font> ';
				if (!defined('DEBUGMODE')) $_SESSION['totaltime'] = round((array_sum(explode(" ",microtime())) - $_SESSION['tb_start_time'] ),4);				
				echo ' Executed in <b> '.$_SESSION['totaltime'].' </b>seconds'.(get_user_class() >= UC_CODER ? ' with <b><a href="'.$BASEURL.'/query_explain.php">'.intval($_SESSION['totalqueries']).'</a></b> queries !' : '').'
			</div>
		</div>
	</div>
</body>
</html>
'
?>