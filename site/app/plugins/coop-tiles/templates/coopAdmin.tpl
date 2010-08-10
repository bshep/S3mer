{include file="header.tpl" title="Coop Administration"}

	<div id="adminContainer1" class="adminContainer">
		<div id="headerTxt1" class="headerTxt">
			Tile Creation
		</div>
		<div id="fields" class="fields">
			<form action="newTile.php" name="newTileForm" method="post">
				<select name="TileType">
					<option value =" ">-Select Tile Type-</option>
					{html_options values=$allTypesId output=$allTypesName}
				</select>
				<select name="Category">
					<option value =" ">-Select Category-</option>
					{html_options values=$allCatsId output=$allCatsName}
				</select>
				<textarea name="description" rows="8" cols="55" id="contentstxt"></textarea>
				<input type="text" name="url" value="" id="url"/>
				<input type="submit" name="some_name" value="Create Tile" id="some_name" />
			</form>
		</div>
	</div>

	<div id="TileList" class="tileList">
		{section name=tiles loop=$alltiles}
		<form action="deleteTile.php" name="deleteTileForm_{$alltiles[tiles].id}" method="post">
			<div id="tileRow_{$alltiles[tiles].id}" class="tileRow">
				<div id="tileType_{$alltiles[tiles].id}" class="tileType">
					{$alltiles[tiles].type}
				</div>
				<div id="tileCategory_{$alltiles[tiles].id}" class="tileCategory">
					{$alltiles[tiles].category}
				</div>
				<div id="tileContents_{$alltiles[tiles].id}" class="tileContents">
					{$alltiles[tiles].content}
				</div>
				<div id="tileURL_{$alltiles[tiles].id}" class="tileURL">
					{$alltiles[tiles].url}
				</div>
				<div id="tileDelete_{$alltiles[tiles].id}" class="tileDelete">
					<input type="hidden" name="idtodelete" value="{$alltiles[tiles].id}" id="idToDelete_{$alltiles[tiles].id}" />
					<input type="submit" name="delete" value="x" id="delete_{$alltiles[tiles].id}" />
				</div>
			</div>
		</form>
		{/section}
	</div>

{include file="footer.tpl"}