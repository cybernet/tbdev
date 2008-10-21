<?php
 #############################################################################
 # IMDBPHP                              (c) Giorgos Giagas & Itzchak Rehberg #
 # written by Giorgos Giagas                                                 #
 # extended & maintained by Itzchak Rehberg <izzysoft@qumran.org>            #
 # http://www.qumran.org/homes/izzy/                                         #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

 /* $Id: imdb.php,v 1.3 2007/02/27 17:36:41 izzy Exp $ */

require ("imdb.class.php");

$movie = new imdb ($_GET["mid"]);

if (isset ($_GET["mid"])) {
  $movieid = $_GET["mid"];
  $movie->setid ($movieid);

  echo '<HTML><HEAD><TITLE>'.$movie->title().' ('.$movie->year().')';
  echo "</TITLE></HEAD>\n<BODY>";

  # Title & year
  echo '<TABLE><TR><TD colspan=3><FONT size=6><B>';
  echo $movie->title().'</B> ('.$movie->year().')</FONT><BR><br>';
  echo "</TD></tr>\n";
  flush();

  # Photo
  echo '<TR><TD rowspan=110 valign=top>';
  if (($photo_url = $movie->photo_localurl() ) != FALSE) {
    echo '<img src="'.$photo_url.'">';
  } else {
    echo "No photo available";
  }

  # AKAs
  echo '</TD><TD valign=top width=120><b>Also known as:</b> </td><td>';
  foreach ( $movie->alsoknow() as $ak){
    echo $ak["title"].": ".$ak["year"].", ".$ak["country"]." (".$ak["comment"].")<BR>";
  }
  echo '</td></tr>';
  flush();

  # Year & runtime
  echo '<TR><TD><B>Year:</B></TD><TD>'.$movie->year().'</TD></TR>';
  echo '<TR><TD valign=top><B>Runtime:</b></TD><TD>';
  echo $movie->runtime ().' minutes</TD></TR>';
  flush();
/*
  # Runtime Line and Runtimes
  echo '<TR><TD valign=top><B>Runtime line:</b></TD><TD>';
  echo $movie->runtime_all().'</TD></TR>';
  echo '<TR><TD valign=top><B>All Runtimes:</b></TD><TD>';
  $runtimes = $movie->runtimes ();
  foreach ($runtimes as $runtime){
    echo $runtime["time"]." min in ".$runtime["country"]." (".$runtime["comment"].")<BR>";
//    if ( ($i+1) != count($runtime)) echo ", ";
  }
  echo '</TD></TR>';
*/

  # MPAA
  echo '<TR><TD><B>MPAA:</b></TD><TD>';
  foreach ($movie->mpaa() as $key=>$mpaa) {
    echo "$key: $mpaa<br>";
  }
  echo '</TD></TR>';

  # Ratings and votes
  echo '<TR><TD><B>Rating:</b></TD><TD>';
  echo $movie->rating().'</TD></TR>';
  echo '<TR><TD><B>Votes:</B></TD><TD>';
  echo $movie->votes().'</TD></TR>';
  flush();

  # Languages
  echo '<TR><TD><B>Languages:</B></TD><TD>';
  $languages = $movie->languages();
  for ($i = 0; $i + 1 < count($languages); $i++) {
    echo $languages[$i].', ';
  }
  echo $languages[$i].'</TD></TR>';
  flush();

  # Country
  echo '<TR><TD><B>Country:</B></TD><TD>';
  $country = $movie->country();
  for ($i = 0; $i + 1 < count($country); $i++) {
    echo $country[$i].', ';
  }
  echo $country[$i].'</TD></TR>';

  # Genres
  echo '<TR><TD><B>Genre:</B></TD><TD>';
  echo $movie->genre().'</TD></TR>';

  echo '<TR><TD><B>All Genres:</B></TD><TD>';
  $gen = $movie->genres();
  for ($i = 0; $i + 1 < count($gen); $i++) {
    echo $gen[$i].', ';
  }
  echo $gen[$i].'</TD></TR>';

  # Colors
  echo '<TR><TD><B>Colors:</B></TD><TD>';
  $col = $movie->colors();
  for ($i = 0; $i + 1 < count($col); $i++) {
    echo $col[$i].', ';
  }
  echo $col[$i].'</TD></TR>';
  flush();

  # Sound
  echo '<TR><TD><B>Sound:</B></TD><TD>';
  $sound = $movie->sound ();
  for ($i = 0; $i + 1 < count($sound); $i++) {
    echo $sound[$i].', ';
  }
  echo $sound[$i].'</TD></TR>';

  echo '<TR><TD valign=top><B>Tagline:</B></TD><TD>';
  echo $movie->tagline().'</TD></TR>';

  #==[ Staff ]==
  # director(s)
  $director = $movie->director();
  echo '<TR><TD valign=top><B>Director:</B></TD><TD><TABLE>';
  for ($i = 0; $i < count($director); $i++) {
    echo '<tr><td width=200>';
    echo '<a href="http://us.imdb.com/Name?'.$director[$i]["imdb"].'">';
    echo $director[$i]["name"].'</a></td><td>';
    echo $director[$i]["role"]."</td></tr>\n";
  }
  echo '</table></td></tr>';

  # Story
  $write = $movie->writing();
  echo '<TR><TD valign=top><B>Writing By:</B></TD><TD><TABLE>';
  for ($i = 0; $i < count($write); $i++) {
    echo '<tr><td width=200>';
    echo '<a href="http://us.imdb.com/Name?'.$write[$i]["imdb"].'">';
    echo $write[$i]["name"].'</a></td><td>';
    echo $write[$i]["role"]."</td></tr>\n";
  }
  echo '</table></td></tr>';
  flush();

  # Producer
  $produce = $movie->producer();
  echo '<TR><TD valign=top><B>Produced By:</B></TD><TD><TABLE>';
  for ($i = 0; $i < count($produce); $i++) {
    echo '<tr><td width=200>';
    echo '<a href="http://us.imdb.com/Name?'.$produce[$i]["imdb"].'">';
    echo $produce[$i]["name"].'</a></td><td>';
    echo $produce[$i]["role"]."</td></tr>\n";
  }
  echo '</table></td></tr>';

  # Music
  $compose = $movie->composer();
  echo '<TR><TD valign=top><B>Music:</B></TD><TD><TABLE>';
  for ($i = 0; $i < count($compose); $i++) {
    echo '<tr><td width=200>';
    echo '<a href="http://us.imdb.com/Name?'.$compose[$i]["imdb"].'">';
    echo $compose[$i]["name"]."</a></td></tr>\n";
  }
  echo '</table></td></tr>';
  flush();

  # Cast
  $cast = $movie->cast();
  echo '<TR><TD valign=top><B>Cast:</B></TD><TD><TABLE>';
  for ($i = 0; $i < count($cast); $i++) {
    echo '<tr><td width=200>';
    echo '<a href="http://us.imdb.com/Name?'.$cast[$i]["imdb"].'">';
    echo $cast[$i]["name"].'</a></td><td>';
    echo $cast[$i]["role"]."</td></tr>\n";
  }
  echo '</table></td></tr>';
  flush();

  # Plot outline & plot
  echo '<tr><td valign=top><b>Plot Outline:</b></td><td>';
  echo $movie->plotoutline().'</td></tr>';

  $plot = $movie->plot();
  echo '<tr><td valign=top><b>Plot:</b></td><td><ul>';
  for ($i = 0; $i < count($plot); $i++) {
    echo "<li>".$plot[$i]."</li>\n";
  }
  echo '</ul></td></tr>';
  flush();

  # Taglines
  $taglines = $movie->taglines();
  echo '<tr><td valign=top><b>Taglines:</b></td><td><ul>';
  for ($i = 0; $i < count($taglines); $i++) {
    echo "<li>".$taglines[$i]."</li>\n";
  }
  echo '</ul></td></tr>';

  # Selected User Comment
  echo '<tr><td valign=top><b>User Comments:</b></td><td>';
  echo $movie->comment().'</td></tr>';

  echo '</TABLE><BR>';
}
?>

