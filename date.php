<?php

$today = new DateTime();
$tgl = $today->format('Y-m-d');

$expire = new DateTime('2 Jan 2025 14:15:11');
$tgl1 = $expire->format('Y-m-d');

if ($tgl != $tgl1) {
	echo "Tgl dan Tgl1 tidak sama";
} else {
	echo "Tgl dan Tgl1 sama";
}
