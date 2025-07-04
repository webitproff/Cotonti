<!-- BEGIN: MAIN -->
<!-- BEGIN: STANDALONE_HEADER -->
<!DOCTYPE html>
<html lang="{PHP.usr.lang}">
	<head>
		<title>{PHP.L.pfs_myFiles} - {PHP.cfg.maintitle}</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<base href="{PHP.cfg.mainurl}/" />
        {PFS_HEAD}
		<link href="{PHP.cfg.themes_dir}/{PHP.theme}/css/{PHP.scheme}.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
<!-- END: STANDALONE_HEADER -->
		<div class="block">
			<h2 class="pfs">{PFS_BREADCRUMBS}</h2>
			<p class="small">{PFS_SUBTITLE}</p>
			{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
			<form id="editfolder" action="{PFS_ACTION}" method="post">
				<table class="cells">
					<!--<tr>
						<td class="width20">{PHP.L.pfs_parentfolder}:</td>
						<td class="width80">{PFF_FOLDER}</td>
					</tr>-->
					<tr>
						<td>{PHP.L.Folder}:</td>
						<td>{PFF_TITLE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Description}:</td>
						<td>{PFF_DESC}</td>
					</tr>
					<tr>
						<td>{PHP.L.Date}:</td>
						<td>{PFF_DATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Updated}:</td>
						<td>{PFF_UPDATED}</td>
					</tr>
					<tr>
						<td>{PHP.L.pfs_ispublic}</td>
						<td>
							{PFF_ISPUBLIC}
						</td>
					</tr>
					<tr>
						<td>{PHP.L.pfs_isgallery}</td>
						<td>
							{PFF_ISGALLERY}
						</td>
					</tr>
					<tr>
						<td colspan="2" class="valid">
							<button type="submit">{PHP.L.Update}</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
<!-- BEGIN: STANDALONE_FOOTER -->
	</body>
</html>
<!-- END: STANDALONE_FOOTER -->

<!-- END: MAIN -->
