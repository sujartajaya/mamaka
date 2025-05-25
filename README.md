# my-radius-admin

DB::select('select guests.\*, countries.country_name, radacct.nasportid from guests, countries, radacct where guests.country_id = countries.id and guests.username = radacct.username');

DB::select('select guests.\*, sum(radacct.acctinputoctets) as jumlah from guests, radacct where guests.username=radacct.username group by radacct.username');

DB::select('select guests.\*, sum(radacct.acctinputoctets) as jumlah, countries.country_name from guests, radacct, countries where guests.username=radacct.username and guests.country_id = countries.id group by radacct.username');

DB::select('select guests.\*, sum(radacct.acctinputoctets) as jumlah, countries.country_name from guests, radacct, countries where guests.username=radacct.username and guests.country_id = countries.id group by radacct.username order by guests.created_at asc limit 2 offset 4');

DB::select("select guests.\*, sum(radacct.acctinputoctets) as byteinput,sum(radacct.acctoutputoctets) as byteoutput, countries.country_name from guests, radacct, countries where guests.username=radacct.username and guests.country_id = countries.id and radacct.acctstarttime >= '2025-05-01' and radacct.acctstarttime < '2025-06-01' group by radacct.username or
der by guests.created_at asc");

{
error: false
title: "Active User"
▼activeuser: [
▼{
.id: "*BE003D0A"
server: "hotspot1"
user: "UTRCHy7kL6"
address: "10.61.0.190"
mac-address: "EA:56:CD:96:F7:72"
login-by: "http-chap"
uptime: "1h25m34s"
idle-time: "1s"
keepalive-timeout: "2m"
bytes-in: "25601756"
bytes-out: "599130461"
packets-in: "213757"
packets-out: "476045"
radius: "true"
}
▼{
.id: "*C1003D0A"
server: "hotspot1"
user: "GvgBEsPUK9"
address: "10.61.0.193"
mac-address: "82:73:35:7B:6E:F8"
login-by: "http-chap"
uptime: "25m3s"
idle-time: "1s"
keepalive-timeout: "2m"
bytes-in: "4070307"
bytes-out: "56300940"
packets-in: "43596"
packets-out: "46860"
radius: "true"
}
▼{
.id: "*C3003D0A"
server: "hotspot1"
user: "q8qfMoSHJx"
address: "10.61.0.195"
mac-address: "82:4F:3C:3A:3A:2E"
login-by: "http-chap"
uptime: "24m57s"
idle-time: "1s"
keepalive-timeout: "2m"
bytes-in: "3835117"
bytes-out: "14432278"
packets-in: "24443"
packets-out: "29922"
radius: "true"
}
▼{
.id: "*C5003D0A"
server: "hotspot1"
user: "7DBGGix1sn"
address: "10.61.0.197"
mac-address: "FA:6C:0E:BE:7E:D3"
login-by: "http-chap"
uptime: "24m47s"
idle-time: "1s"
keepalive-timeout: "2m"
bytes-in: "5121476"
bytes-out: "9014837"
packets-in: "60070"
packets-out: "63336"
radius: "true"
}
▼{
.id: "*C7003D0A"
server: "hotspot1"
user: "Di9IgXLYT7"
address: "10.61.0.199"
mac-address: "7A:47:8F:46:E8:06"
login-by: "http-chap"
uptime: "22m12s"
idle-time: "1s"
keepalive-timeout: "2m"
bytes-in: "14551516"
bytes-out: "6421634"
packets-in: "67078"
packets-out: "60826"
radius: "true"
}
]
}
