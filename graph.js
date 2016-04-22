/** Cette fonction permet de dessiner un graphe d'après un objet JSON (encodé depuis PHP) */
function draw_graph(graph,divId) {
  var width = 750, height = 400, color = d3.scale.category10();
  var links = [], nodes = graph.vertices;
  graph.arrows.forEach(function(link) { links.push({source: link.tail, target: link.head}); });

  var force = d3.layout.force()
      .nodes(nodes)
      .links(links)
      .size([width, height])
      .linkDistance(150)
      .charge(-500)
      .start();

  /* création de la balise svg à l'intérieur de celle dont l'id est 'divId' */
  var svg = d3.select(divId).append("svg")
      .attr("class", "graph")
      .attr("width", width)
      .attr("height", height);

  /* création du marker 'end' qui représente la pointe de la flêche */
  svg.append("svg:defs").selectAll("marker")
      .data(["end"])
    .enter().append("svg:marker")
      .attr("id", String)
      .attr("viewBox", "0 -5 10 10")
      .attr("refX", 22)
      .attr("refY", 0)
      .attr("markerWidth", 8)
      .attr("markerHeight", 6)
      .attr("orient", "auto")
    .append("svg:path")
      .attr("d", "M0,-5 L10,0 L0,5");

  /* ajout des flêches du graphe */
  var path = svg.append("svg:g").selectAll("line")
      .data(force.links())
    .enter().append("line")
      .attr("marker-end", "url(#end)");

  /* ajout des sommets du graphe */
  var node = svg.selectAll(".node")
      .data(force.nodes())
    .enter().append("g")
      .attr("class", "node")
      .call(force.drag);

  node.append("circle")
      .attr("r", 15)
      .style("fill", function(d) { return color(d.index); });

  /* ajout des étiquettes des sommets */
  node.append("text")
      .attr("x", 0)
      .attr("dy", 4)
      .attr("text-anchor", "middle")
      .text(function(d) { return d.name; });

  force.on("tick", function() {
    path.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

        node.attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });
  });
}
