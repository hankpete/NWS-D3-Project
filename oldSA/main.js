;(function() {

  var copy,
      coords = [],
      width = 800,
      height = 400,
      margin = 50,

      line = d3.svg.line(),

      dragStart = function() {
        coords = [];
        g.selectAll("path").remove();
      },

      drawPath = function(terminator) {
        g.append("path").attr({
          d: line(coords)
        });
        if (terminator) {
          g.select("#terminator").remove();
          g.append("path").attr({
            id: "terminator",
            d: line([coords[0], coords[coords.length-1]])
          });
        }
      },

      dragMove = function() {
        dot.classed("selected", false);
        coords.push(d3.mouse(this));
//console.log("this :"+this)
        dot.each(function(d, i) {
          point = [d3.select(this).attr("cx"), d3.select(this).attr("cy")];
          if (pointInPolygon(point, coords)) {
            d3.select(this).classed("selected", true)
          }
        });
        drawPath();
      },

      dragEnd = function() {
        drawPath(true);
      }

      drag = d3.behavior.drag()
                .on("dragstart", dragStart)
                .on("drag", dragMove)
                .on("dragend", dragEnd),

      // from https://github.com/substack/point-in-polygon
      pointInPolygon = function (point, vs) {
console.log("point: "+point+" vs: "+vs)
        // ray-casting algorithm based on
        // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
        var xi, xj, i, intersect,
            x = point[0],
            y = point[1],
            inside = false;

        for (var i = 0, j = vs.length - 1; i < vs.length; j = i++) {
          xi = vs[i][0],
          yi = vs[i][1],
          xj = vs[j][0],
          yj = vs[j][1],

console.log("i: "+i+" j:"+j)
console.log("vs[i]: "+vs[i]+" vs[j]: "+vs[j])
console.log("yi: "+yi+" y: "+y+" yj: "+yj+" xi: "+xi+" x: "+x+" xj: "+xj)

          intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
          if (intersect) inside = !inside;
        }
        return inside;
      },

      svg = d3.select("#canvas").append("svg").attr({
                width: width,
                height: height
              }).call(drag),

      g = svg.append("g"),

      randPoint = function(min, max) {
        return Math.floor(Math.random() * (max-min)) + min;
      },

      points = function(numPoints) {
        var data = [];
        for (i=0; i < numPoints; i++) {
          data.push({x: randPoint(margin, width-margin), y: randPoint(margin, height-margin)});
        }
        return data;
      },

      dot = g.selectAll("circle")
                .data(points(1000))
              .enter().append("circle")
                .attr({
                  cx: function(d, i) {
                    return d.x;
                  },
                  cy: function(d, i) {
                    return d.y;
                  },
                  r: 3
                });

  d3.select("#clear").on("click", function() {
    dot.classed("selected", false);
    dragStart();
  })

}());
