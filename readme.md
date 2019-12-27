# Belgium Postal Codes GeoJSON to SVG

Repo Contains:
--------------

 * GeoJSON of per postal code
 * SVG (final result) of Belgium
 * Laravel Commands to request info / generate SVG (raw versions)

Steps taken:
------------

 * Use https://nominatim.openstreetmap.org/ to search for location. (automated)
 * Grab the OSM id of the boundary (relation) that matches with postal code (mostly automated)
 * Use http://polygons.openstreetmap.fr/?id=2454638 (paste correct ID in URL) to retrieve GeoJSON 
    * First use form at bottom (automated) to simplify with X = 0 (don't expand / narrow the boundaries)
    * Then go to http://polygons.openstreetmap.fr/get_geojson.py?id=2454638&params=0.000000-0.001000-0.001000 (paste ID in URL) for the GeoJSON
 * In case of missing parts, locate the missing parts, and merge into JSON

LICENSE:
--------

MIT
