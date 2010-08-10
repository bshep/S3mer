{include file="header.tpl" title="Tiles2 Index Page"}
		
		<div id="container" class="container">
			{section name=tiles loop=$alltiles}
				<div id="tile" class="tile">
					{if $alltiles[tiles].type eq 1}
						<div id="tile" class="tile {cycle values="color1,color2,color3,color4,color5,color6,color7"}">
							<div id="txtContainerTxt" class="txtContainerTxt">
								<span class="category">{$alltiles[tiles].category}</span><br />
								<span class="txt">{$alltiles[tiles].content}</span>
							</div>
						</div>
					{/if}
					{if $alltiles[tiles].type eq 2}
						<div id="pic" class="pic">
							<img src="{$alltiles[tiles].url}" />
						</div>
						<div id="txtContainerPhoto" class="txtContainerPhoto">
							<span class="category">{$alltiles[tiles].category}</span><br />
							<span class="txt">{$alltiles[tiles].content}</span>
						</div>
					{/if}
					{if $alltiles[tiles].type eq 4}
						<div id="video" class="video">
							<object width="200" height="200"><param name="movie" value="{$alltiles[tiles].url}&autoplay=1&loop=1"></param><param name="wmode" value="transparent"></param><embed src="{$alltiles[tiles].url}&autoplay=1&loop=1" type="application/x-shockwave-flash" wmode="transparent" width="200" height="200"></embed></object>
						</div>
						<div id="txtContainerVid" class="txtContainerVid">
							<span class="category">{$alltiles[tiles].category}</span>
						</div>
					{/if}
					{if $alltiles[tiles].type eq 5}
						<div id="video" class="video">
							<object type="application/x-shockwave-flash" width="200" height="200" data="{$alltiles[tiles].url}&autoplay=1&loop=1"><param name="quality" value="best" />	<param name="allowfullscreen" value="true" /><param name="scale" value="showAll" />	<param name="movie" value="{$alltiles[tiles].url}&autoplay=1&loop=1" /></object>
						</div>
						<div id="txtContainerVid" class="txtContainerVid">
							<span class="category">{$alltiles[tiles].category}</span>
						</div>
					{/if}
				</div>
			{/section}
		</div>
{include file="footer.tpl"}