<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>D3 Test</title>
	<script src="d3/d3.min.js"></script>
	<script src="d3/topojson.min.js"></script>
	<style>
		* {
			margin: 0;
			padding: 0;
		}

		svg {
			position: absolute;
			width: 100%;
			height: 100%;
		}

		#zone-borders {
		  fill: none;
		  stroke-width: 1px;
		  stroke-linejoin: round;
		  stroke-linecap: round;
		  pointer-events: none;
	    }

		#ids {
			font-family: Roboto;
			font-size: 10px;
			text-anchor: middle;
			opacity: .75;
		}

		#moused-zone {
			fill: #5a5;
		}

		text.cities {
			font-family: Roboto;
			font-size: 15px;
		}

		.category-text, .loading {
			font-family: Roboto;
			font-size: 36px;
		}

		.min-text, .max-text {
			font-family: Roboto;
			font-size: 15px;
		}
	</style>
</head>
<body>

	<div id="category-div" style="display: none;">
    <?php
      $cats = ["elev", "Temp", "TempC", "Dewp", "Relh",
					"Winds", "SLP", "Altimeter", "Visibility", "TempHi24",
					"TempLo24", "RelhHi24", "RelhLo24", "GustHi24",
					"RawsTemp12", "RawsTemp13", "RawsTemp14", "RawsRelh12",
					"RawsRelh13", "RawsRelh14"];
      $catsLength = count($cats);

      for ($i = 0; $i < $catsLength; $i++)
      {
        if( $_GET[ $cats[$i] ] == "on" )
        {
					echo "<p>";
          echo htmlspecialchars($cats[$i]);
					echo "</p>";
				}
      }
    ?>
	</div>

	<div id="radius-div" style="display: none;">
		<?php
			$radius = $_GET[ "radius" ];
			echo "<p>";
			echo htmlspecialchars($radius);
			echo "</p>";
		?>
	</div>

	<div id="circleOpacity-div" style="display: none;">
		<?php
			$circleOpacity = $_GET[ "circleOpacity" ];
			echo "<p>";
			echo htmlspecialchars($circleOpacity);
			echo "</p>";
		?>
	</div>

	<div id="dt-div" style="display: none;">
		<?php
			$dt = $_GET[ "dt" ];
			echo "<p>";
			echo htmlspecialchars($dt);
			echo "</p>";
		?>
	</div>

	<div id="delay-div" style="display: none;">
		<?php
			$delay = $_GET[ "delay" ];
			echo "<p>";
			echo htmlspecialchars($delay);
			echo "</p>";
		?>
	</div>

	<div id="showIds-div" style="display: none;">
		<?php
			$showIds = $_GET[ "showIds" ];
			echo "<p>";
			echo htmlspecialchars($showIds);
			echo "</p>";
		?>
	</div>

	<div id="showCities-div" style="display: none;">
		<?php
			$showCities = $_GET[ "showCities" ];
			echo "<p>";
			echo htmlspecialchars($showCities);
			echo "</p>";
		?>
	</div>

	<div id="bgColor-div" style="display: none;">
		<?php
			$bgColor = $_GET[ "bgColor" ];
			echo "<p>";
			echo htmlspecialchars($bgColor);
			echo "</p>";
		?>
	</div>

	<div id="zoneColor-div" style="display: none;">
		<?php
			$zoneColor = $_GET[ "zoneColor" ];
			echo "<p>";
			echo htmlspecialchars($zoneColor);
			echo "</p>";
		?>
	</div>

	<div id="borderColor-div" style="display: none;">
		<?php
			$borderColor = $_GET[ "borderColor" ];
			echo "<p>";
			echo htmlspecialchars($borderColor);
			echo "</p>";
		?>
	</div>

	<div id="zoneTextColor-div" style="display:none">
		<?php
		 	$zoneTextColor = $_GET[ "zoneTextColor" ];
			echo "<p>";
			echo htmlspecialchars($zoneTextColor);
			echo "</p>";
		?>
	</div>

	<div id="cityTextColor-div" style="display:none">
		<?php
			$cityTextColor = $_GET[ "cityTextColor" ];
			echo "<p>";
			echo htmlspecialchars($cityTextColor);
			echo "</p>";
		?>
	</div>

	<div id="minTextColor-div" style="display:none">
		<?php
			$minTextColor = $_GET[ "minTextColor" ];
			echo "<p>";
			echo htmlspecialchars($minTextColor);
			echo "</p>";
		?>
	</div>

	<div id="maxTextColor-div" style="display:none">
		<?php
			$maxTextColor = $_GET[ "maxTextColor" ];
			echo "<p>";
			echo htmlspecialchars($maxTextColor);
			echo "</p>";
		?>
	</div>

	<div id="catTextColor-div" style="display:none">
		<?php
			$catTextColor = $_GET[ "catTextColor" ];
			echo "<p>";
			echo htmlspecialchars($catTextColor);
			echo "</p>";
		?>
	</div>

	<div id="radiusMin-div" style="display:none">
		<?php
			$radiusMin = $_GET[ "radiusMin" ];
			echo "<p>";
			echo htmlspecialchars($radiusMin);
			echo "</p>";
		?>
	</div>

	<div id="radiusMax-div" style="display:none">
		<?php
			$radiusMax = $_GET[ "radiusMax" ];
			echo "<p>";
			echo htmlspecialchars($radiusMax);
			echo "</p>";
		?>
	</div>

	<script>
		//begin by getting the variables put into DOM by form/php

		//set categories variable from the form
		var div = document.getElementById("category-div");
		var myData = div.children;

		var categories = [];
		for (var i = 0; i < myData.length; i++) {
			categories.push(myData[i].textContent);
		}

		//set radius
		div = document.getElementById("radius-div");
		var radius = parseFloat(div.children[0].textContent);

		//set opacity
		div = document.getElementById("circleOpacity-div");
		var circleOpacity = parseFloat(div.children[0].textContent);

		//set dt
		div = document.getElementById("dt-div");
		var dt = parseInt(div.children[0].textContent);

		//set delay
		div = document.getElementById("delay-div");
		var delay = parseInt(div.children[0].textContent);

		//set showIds
		div = document.getElementById("showIds-div");
		var showIds = div.children[0].textContent;

		//set showCities
		div = document.getElementById("showCities-div");
		var showCities = div.children[0].textContent;

		//set bgColor
		div = document.getElementById("bgColor-div");
		var bgColor  = div.children[0].textContent;

		//set zoneColor
		div = document.getElementById("zoneColor-div");
		var zoneColor  = div.children[0].textContent;

		//set borderColor
		div = document.getElementById("borderColor-div");
		var borderColor = div.children[0].textContent;

		//set zoneTextColor
		div = document.getElementById("zoneTextColor-div");
		var zoneTextColor = div.children[0].textContent;

		//set cityTextColor
		div = document.getElementById("cityTextColor-div");
		var cityTextColor = div.children[0].textContent;

		//set minTextColor
		div = document.getElementById("minTextColor-div");
		var minTextColor = div.children[0].textContent;

		//set maxTextColor
		div = document.getElementById("maxTextColor-div");
		var maxTextColor = div.children[0].textContent;

		//set catTextColor
		div = document.getElementById("catTextColor-div");
		var catTextColor = div.children[0].textContent;

		//set radiusMin
		div = document.getElementById("radiusMin-div");
		var radiusMin = parseFloat(div.children[0].textContent);

		//set radiusMax
		div = document.getElementById("radiusMax-div");
		var radiusMax = parseFloat(div.children[0].textContent);




		//set up variables and functions we will need
		var width = window.innerWidth,
			height = window.innerHeight;

		var projectionFunc = d3.geo.mercator()	//world
			.scale(7000)
			.center([-119.6, 36.3])	//hnx coords
			.translate([width/2, height/2]);

		var pathFunc = d3.geo.path()
			.projection(projectionFunc);

		var svg = d3.select("body").append("svg")
			.attr("width", width)
			.attr("height", height);

		svg.append("rect")
			.attr("x", "0")
			.attr("y", "0")
			.attr("width", width)
			.attr("height", height)
			.attr("fill", bgColor);

		var gMap = svg.append("g");
		var gVis = svg.append("g");

		//get hnx map data and plot it
		d3.json("hnxSaZones.json", function(error, hnx) {
			if (error) throw error;

			var zones = topojson.feature(hnx, hnx.objects.hnxsaZones).features;
			gMap.selectAll("path")
					.data(zones)
				.enter()
					.append("path")
					.attr("id", "zones")
					.attr("fill", zoneColor)
					.attr("d", pathFunc);

			//make borders separately so they dont slow it down
			gMap.append("path")
		        .datum(topojson.mesh(hnx, hnx.objects.hnxsaZones, function(a, b) {
				    return a !== b;
			    }))
		        .attr("id", "zone-borders")
				.attr("stroke", borderColor)
		        .attr("d", pathFunc);

			//label zones if asked
			if (showIds == "yes") {
				gMap.selectAll("text")
						.data(zones)
		  	  .enter()
						.append("text")
			  		.attr("id", "ids")
						.attr("fill", zoneTextColor)
						.attr("x", function(d) {
							return projectionFunc([d.properties.LON, d.properties.LAT])[0];
						})
						.attr("y", function(d) {
							return projectionFunc([d.properties.LON, d.properties.LAT])[1];
						})
						.text(function(d) {
							return d.properties.ZONE;
						});
			}
		});




		////////////////////////////////////////
		///				main vis 								//////
		////////////////////////////////////////



		//main loop
		var index = 0;
		main();


		function main() {
			//put all the vis on the svg, make the heatmap, cycle through
			//changing opacity on the categories selected (default all)

			//show "loading" while they wait
			gMap.selectAll("text.loading")
					.data(["Loading..."])
				.enter()
					.append("text")
					.attr("class", "loading")
					.attr("x", width/15)
					.attr("y", height/15)
					.attr("fill", catTextColor)
					.text(function(d) { return d; });

			//get the data and run initialize() for each category
			// var offlineData = "offlineData.json";
			var dataUrl = "ba-simple-proxy/ba-simple-proxy.php?url=http://www.wrh.noaa.gov/hnx/JimBGmwXJList.php?extents=34.74,-121.4,38.36,-117.62&mode=native";
			d3.json(dataUrl, function(error, data) {
			// d3.json(offlineData, function(error, data) {
				if (error) { console.log("There was an error loading the data." + error); }

				for (var i = 0; i < categories.length; i++) {
					initialize(data.stations, categories[i])
				}

				//label cities if asked (put in this block so it does it after data is loaded
				if (showCities=="yes") {
					d3.json("citiesJson.json", function(error, data) {
						if (error) { console.log("There was an error loading the cities data: " + error); }

						var cities = data.cities;

						gVis.selectAll("circle.cities")
								.data(cities)
							.enter()
								.append("circle")
								.attr("class", "cities")
								.attr("cx", function(d) {
									return projectionFunc( [d.LON, d.LAT] )[0];
								})
								.attr("cy", function(d) {
										return projectionFunc( [d.LON, d.LAT] )[1];
								})
								.attr("r", 3)
								.attr("fill", cityTextColor);

						gVis.selectAll("text.cities")
								.data(cities)
							.enter()
								.append("text")
								.attr("class", "cities")
								.attr("x", function(d) {
									return projectionFunc( [d.LON, d.LAT] )[0] + 5;
								})
								.attr("y", function(d) {
									return projectionFunc( [d.LON, d.LAT] )[1];
								})
								.attr("fill", cityTextColor)
								.text(function(d) { return d.name; });
					});
				}
			});

			//take away loading
			gMap.selectAll("text.loading")
					.data([])
				.exit()
				.transition()
					.duration(10000)
					.style("opacity", 0)
					.remove();

			makeLegend();
			cycle();
			index++;
			setInterval(function() {
						cycle();
						index++;
					}, 2*dt + delay);
		}


		function makeLegend() {
			//make a heatmap bar to show what the data represents
			var numRects = 100;
			var rectsData = [];
			for (var i = 0; i < numRects; i++) {
				rectsData.push(i);
			}
			var textHeight = height/15;
			var categoryTextWidth = width/4;
			var colorScale = d3.scale.linear()
					.domain([0, numRects - 1])
					.range([0, 255]);

			gVis.selectAll("rect.heatmap")
					.data(rectsData)
				.enter()
					.append("rect")
					.attr("x", function(d, i) {
						return 2.2*categoryTextWidth + i*(.75*categoryTextWidth/numRects);
					})
					.attr("y", textHeight/2)
					.attr("width", categoryTextWidth/numRects)
					.attr("height", textHeight/2)
					.attr("fill", function(d) {
						var num = Math.round(colorScale(d));
						return "rgb(" + num + ", 0, " + (255 - num) + ")";
					});
		}


		function cycle() {
			//pick next data category, make it opacity 1 and the
			//one before it opacity 0 in a smooth transition
			var nextCategory = categories[index % categories.length];
			var previousCategory = categories[(index-1) % categories.length];

			gVis.selectAll("#" + previousCategory)		//above
				.transition()
					.duration(dt)
					.style("opacity", 0);

			gVis.selectAll("text#" + nextCategory)		//below
				.transition()
					.duration(dt)
					.style("opacity", 1);

			gVis.selectAll("circle#" + nextCategory)		//below
				.transition()
					.duration(dt)
					.style("opacity", circleOpacity);
		}


		function average(data) {
			//returns mean of data
			var sum = 0;
			for (var i = 0; i < data.length; i++) {
				sum += data[i];
			}
			var average = sum/data.length;
			return average;
		}


		function initialize(dataFull, name) {
			//from the data, plot the heatmapped dots in their
			//coordinates and start them off opacity 0.
			//also display some text for what the colors mean
			//and what the data is about
			var dataList = [];
			var newNum;
			for (var i = 0; i < dataFull.length; i++) {
				newNum = eval("dataFull[i]." + name);
				if (typeof newNum == "string" && newNum != "") {
					dataList.push( parseFloat(newNum) );
				}
			}

			var avg = average(dataList);

			var diffs = [];
			for (var i = 0; i < dataList.length; i++) {
				diff = Math.abs(dataList[i] - avg)
				diffs.push( Math.pow(diff, 2) );
			}
			var sum = 0;
			for (var i = 0; i < diffs.length; i++) {
				sum += diffs[i];
			}
			var stdv = Math.sqrt(sum/diffs.length);

			dataList.sort( function(a, b) { return a-b; });

			for (var i = 0; i < dataList.length; i++) {
				if (dataList[i] > (avg - 3*stdv)) {
					var min = dataList[i];
					break;
				}
			}
			for (var i = 0; i < dataList.length; i++) {
				if (dataList[dataList.length - i - 1] < (avg + 3*stdv)) {
					var max = dataList[dataList.length - i - 1];
					break;
				}
			}

			var colorScale = d3.scale.linear()
					.domain([min, max])
					.range([0, 255]);

			var radiusScale =	d3.scale.linear()
							.domain([min, max])
							.range([radiusMin, radiusMax]);


			//add data vis with class 'name' and opacity 0
			gVis.selectAll("circle." + name)
					.data(dataFull)
				.enter()
					.append("circle")
					.attr("class", function(d) {
						if (eval("d." + name)) {
							return name;
						} else {
							return "undefined";
						}
					})
					.attr("id", name)
					.attr("cx", function(d) {
						return projectionFunc([d.longitude, d.latitude])[0]
					})
					.attr("cy", function(d) {
						return projectionFunc([d.longitude, d.latitude])[1]
					})
					.attr("r", function(d) {
						var r = radiusScale( eval("d." + name) );
						if (r) {		//make sure its not one of the undefined
							if (r > radiusMax || r < radiusMin) {
								return 1;
							} else {
								return r;
							}
						} else {
							return 0;
						}
					})
					.attr("fill", function(d) {
						var num = Math.round(colorScale(eval("d." + name)));
						if (num < 0 || num > 255) {
							return "rgb(0, 0, 0)";
						}
						return "rgb(" + num + ", 0, " + (255 - num)+ ")";
					})
					.style("opacity", 0);
			//the ones that had no data
			gVis.selectAll("circle.undefined")
					.remove();

			//display name of category
			var textHeight = height/15;
			var categoryTextWidth = width/4;
			gVis.selectAll("text#" + name + ".category-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "category-text")
					.attr("id", name)
					.attr("x", categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", catTextColor)
					.text(name)
					.style("opacity", 0);

			//show a legend for the color map
			gVis.selectAll("text#" + name + ".min-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "min-text")
					.attr("id", name)
					.attr("x", 2*categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", minTextColor)
					.text(min)
					.style("opacity", 0);
			gVis.selectAll("text#" + name + ".max-text")
					.data([0])
				.enter()
					.append("text")
					.attr("class", "max-text")
					.attr("id", name)
					.attr("x", 3*categoryTextWidth)
					.attr("y", textHeight)
					.attr("fill", maxTextColor)
					.text(max)
					.style("opacity", 0);
		}


	</script>

</body>
</html>
