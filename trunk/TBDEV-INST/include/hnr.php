<?php
/////cddvd's fully automatic hitrun script--modified for new and improved snatchlist by Bigjoos/////////
$pwsecs = 7*86400;
$pwdt = sqlesc(get_date_time(time() + $pwsecs));
$now = sqlesc(get_date_time());
//Remove warning if seeding//
$res = mysql_query("SELECT userid, torrentid, torrent_name, hit_run FROM snatched WHERE hit_run='2'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$res1 = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid=$user AND torrent = $torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res1) > 0) {
$res3=mysql_query("SELECT class,hit_run_total FROM users WHERE id = $user");
$arr3 = mysql_fetch_array($res3);
$hrtotal = $arr3['hit_run_total'];
$hrtotal = $hrtotal - 1;
if ($hrtotal < 0) {
$hrtotal=0;
}
$class = $arr3['class'];
if ($class >= '4') {
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
write_log("Checking Take Warn Off Script UserId : $user Class : $class Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE snatched SET hit_run = '0' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$msg = sqlesc("Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - Thank You For Re-seeding.If you fail to Re-Seed to a 0.8 Ratio or 36 hours it will be re-instated - Hit-Run Total=$hrtotal\n");
$subject = sqlesc("Warning Removal.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - Re-Seed to a 0.8 Ratio or 36 hours or it will be re-instated.\n";
$modcom = sqlesc($modcomment);
if ($hrtotal<6) {
mysql_query("UPDATE users SET warned = 'no', downloadpos='yes', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take pre warn off & Re-enable Downloads (Reseed to 0.8 or 36 hours) $user Snatched Id : ($torrent) - ($torrentname) - Lookup:$pwdt Now:$now Hit-Run Script:$hit_run");
$msg = sqlesc("Your HitRun Total Is now below limit - downloads are enabled again.Thanks for seeding!\n");
$subject = sqlesc("Hit Run total updated.");
} else {
mysql_query("UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off but still disabled Dloads (Reseeded to 0.8 or 36 hours) $user Snatched Id ($torrent) - ($torrentname) - Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
$msg = sqlesc("Your HitRun Total Is still over the allowed limit - downloads are still Disabled\n");
$subject = sqlesc("Hit Run total updated.");
}
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
}}}}
//Remove warning if 0.8 ratio but stopped seeding before next check on cleanup//
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, downloaded, uploaded, hit_run FROM snatched WHERE hit_run='2'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
if ($sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
$res1=mysql_query("SELECT hit_run_total FROM users WHERE id = $user");
$arr1 = mysql_fetch_array($res1);
$hrtotal = $arr1['hit_run_total'];
$hrtotal =$hrtotal - 1;
if ($hrtotal < 0) {
$hrtotal=0;
}
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$subject = sqlesc("Warning Removed");
$msg = sqlesc("Your Warning Has Been Removed for Snatch Id : $torrent - $torrentname - Thank You For Re-seeding to a 0.8 Ratio OR 36 hours H&R Total=$hrtotal\n");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Your Warning Has Been Removed for Snatched Id : $torrent - $torrentname - User Re-Seeded to a 0.8 Ratio or 36 hours\n";
$modcom = sqlesc($modcomment);
If ($hrtotal<6) {
mysql_query("UPDATE users SET warned = 'no', downloadpos='yes', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off (Reseeded) $user Snatched Id : ($torrent) - ($torrentname) Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE users SET warned = 'no', warneduntil = '0000-00-00 00:00:00', hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Take warn off (Reseeded) $user Snatched Id : ($torrent) - ($torrentname) - Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
$msg = sqlesc("Your Hit-Run Total Is still over the allowed limit downloads are still Disabled - Hit-Run Total=$hrtotal\n");
$subject = sqlesc("Hit run limit exceeded.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__); //== uncomment then comment out above query to use subject in pm's
}}}}
///warn after re-seed request from tracker if not re-seeded////
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, uploaded, downloaded, hit_run FROM snatched WHERE prewarn<NOW() AND hit_run='1' AND finished='yes'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$torrentname = $arr['torrent_name'];
$hit_run=$arr['hit_run'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
$up=mksize($arr['uploaded']);
$down=mksize($arr['downloaded']);
$ratio=number_format((100/$down)*$up);
$res2=mysql_query("SELECT class,hit_run_total FROM users WHERE id =$user");
$arr2 = mysql_fetch_array($res2);
$class = $arr2['class'];
$hrtotal = $arr2['hit_run_total'];
$hrtotal = $hrtotal +1;
$rescheck = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid = $user AND torrent=$torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($rescheck) < 1) {
if ($class >= '4' OR $sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
write_log("Did not warn UserId :$user Class : $class Lookup : $pwdt Now : $now Hit-Run Script : $hit_run");
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
} else {
$msg = sqlesc("Warning You Refused Or Were Unable To Re-Seed Snatched Id : ($torrent) - ($torrentname) - within the 3 hours so you have received an automatic 7 days warning If you Re-seed it Now or at a later Time/Date this warning will be automatically removed from your account Hit and run Total= $hrtotal Stats : Up : $up Down : $down Ratio : $ratio\n");
$subject = sqlesc("Reseed Warning.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
mysql_query("UPDATE snatched SET hit_run = '2' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Warning You Refused To Re-Seed Snatched Id : $torrent - $torrentname - after 3 hours. If you Now Re-Seed It At Any Time It Will Still Be automatically Removed From Your Account - Stats : Up: $up Down : $down Ratio : $ratio\n";
$modcom = sqlesc($modcomment);
If ($hrtotal<=5){
mysql_query("UPDATE users SET warned = 'yes', warneduntil = $pwdt,hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Warned user - $user Snatch Id : ($torrent) - ($torrentname) Stats : Lookup : $pwdt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
} else {
mysql_query("UPDATE users SET warned = 'yes', downloadpos='no', warneduntil = $pwdt,hit_run_total=$hrtotal,modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Warned User & Disabled Dloads $user Snatched Id : ($torrent) - ($torrentname) - Stats : Lookup : $pwdt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
$msg = sqlesc("Your Hit and Run Total Is Now Over The Allowed Limit. All Downloads are Now Disabled.\n");
$subject = sqlesc("Download Disable Warning.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__);
}}}}}
//HitRun - Send a request to reseed if ratio less then 0.8 on torrent and stopped seeding//
$secs = 0.0416*86400;
$dt = sqlesc(get_date_time(time() - $secs));
$wsecs = 0.125*86400;
$wdt = sqlesc(get_date_time(time() + $wsecs));
$res = mysql_query("SELECT userid, torrentid, torrent_name, seedtime, uploaded, downloaded, hit_run FROM snatched WHERE uploaded < ((downloaded/100)*80) and UNIX_TIMESTAMP($dt)<UNIX_TIMESTAMP(last_action) and hit_run='0' and finished='yes'");
if (mysql_num_rows($res) > 0) {
while ($arr = mysql_fetch_assoc($res)) {
$user=$arr['userid'];
$torrent=$arr['torrentid'];
$hit_run=$arr['hit_run'];
$torrentname=$arr['torrent_name'];
$sdtime = $arr['seedtime'] / 86400;
$uploaded = (($arr['downloaded']/100)*80);
$up=mksize($arr['uploaded']);
$down=mksize($arr['downloaded']);
$ratio=number_format((100/$down)*$up);
$res2=mysql_query("SELECT class FROM users WHERE id = $user");
$arr2 = mysql_fetch_array($res2);
$class = $arr2['class'];
$res3 = mysql_query("SELECT * FROM peers WHERE seeder='yes' AND userid = $user AND torrent=$torrent") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res3) < 1) {
if ($class >= '4' OR $sdtime >= '1.5' OR $arr['uploaded'] >= $uploaded) {
mysql_query("UPDATE snatched SET hit_run = '3' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
} else {
$msg = sqlesc("Seeding Request - Please Re-Seed Snatched Id : $torrent - $torrentname - If you fail to do so after 3 hours you will get an automatic warning Unless -- You Re-Seed, If you do Re-seed it Now Or At Anytime (Even After A Warning Has Been GIven) The Warning Is automatically removed from your account $dt Up : $up Down : $down \n.");
$subject = sqlesc("HnR Reseed Request.");
mysql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES(0, $user, $now, $msg, 0, $subject)") or sqlerr(__FILE__, __LINE__); //== uncomment then comment out above query to use subject in pm's
mysql_query("UPDATE snatched SET prewarn = $wdt, hit_run = '1' WHERE userid = $user and torrentid=$torrent") or sqlerr(__FILE__, __LINE__);
$modcomment = date("Y-m-d") . " - Re-Seed Request Sent for Snatched Id : $torrent - $torrentname - Up : $up Down : $down Ratio : $ratio\n";
$modcom = sqlesc($modcomment);
mysql_query("UPDATE users SET modcomment = CONCAT($modcom,modcomment) WHERE id = $user") or sqlerr(__FILE__, __LINE__);
write_log("Pre-Warn user UserId : $user Snatched Id : ($torrent) - ($torrentname) - Lookup : $dt Now : $now Up : $up Down : $down Ratio : $ratio Hit-Run Script : $hit_run");
}}}}
///////end hitrun script////////
?>