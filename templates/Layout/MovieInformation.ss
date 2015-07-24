<% with getInfo %>
	<div id="table">
		<div id="image" class="left">
			<img src="$poster" alt="$title ($year) poster" title="$title ($year) poster" class="poster"/>
		</div>
		<div id="overview">
			<h1 id="heading" class="center">
				<span class="title">$title</span>&nbsp;<span class="year">($year)</span></h1>
			<div id="information" class="center top-info">
				<span class="contentRating">$rated</span>
				|
				<span class="runningTime">$runtime</span>
				|
				<span class="genres">$genre</span>
				|
				<span class="releaseDate">$released</span>
			</div>
			<div id="ratings" class="center top-info">
				<span class="imdb">IMDb: (<span class="rating">$imdbRating / 10</span>)</span>
				|
				<span class="metacritic">Metacritic: (<span class="rating">$metascore / 100</span>)</span>
				|
				<span class="rottentomatoes">Rotten tomatoes (<span class="rating">$tomatoRating / 10</span>)</span>
			</div>
			<div class="plot">
				$plot
			</div>
			<div class="people">
				<div class="director">
					<h4 class="inline">Director:</h4>$director
				</div>
				<div class="writer">
					<h4 class="inline">Writer:</h4>$writer
				</div>
				<div class="actors">
					<h4 class="inline">Actors:</h4>$actors
				</div>
			</div>
		</div>
	</div>
<% end_with %>
