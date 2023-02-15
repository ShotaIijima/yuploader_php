<nav class="navbar navbar-expand navbar-dark bg-dark">
  <a class="navbar-brand" href="/"><?=h($_SESSION['user']['name'])?>様</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExample02">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="/setting.php">設定</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" target="_brank" href="/yconnect.php">Token取得</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" target="_brank" href="/brands.php">ブランドコード一覧</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" target="_brank" href="/pcates.php">プロダクトカテゴリ一覧</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/logout.php?token=<?=h(generate_token())?>">ログアウト</a>
      </li>
    </ul>
  </div>
</nav>

<?php if( !empty($ERRORS) ): ?>
	<ul class="error_list">
	<?php foreach( $ERRORS as $value ): ?>
		<li><?php echo $value; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if( !empty($INFOS) ): ?>
	<ul class="info_list">
	<?php foreach( $INFOS as $value ): ?>
		<li><?php echo $value; ?></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
