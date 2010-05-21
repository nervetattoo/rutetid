<?php

/**
 * Convert UTM location to LL location
 *
 * @author Adrian Dvergsdal <adrian@keyteq.no>
 */
class ConvertCoordinate
{
    /**
     * Convert UTM to Longitude/Latitude
     *
     * Equations from USGS Bulletin 1532
     * East Longitudes are positive, West longitudes are negative.
     * North latitudes are positive, South latitudes are negative
     * Lat and Long are in decimal degrees.
     * Written by Chuck Gantz- chuck dot gantz at globalstar dot com, converted to PHP by
     * Brenor Brophy, brenor dot brophy at gmail dot com
     *
     * If a value is passed for $LongOrigin then the function assumes that
     * a Local (to the Longitude of Origin passed in) Transverse Mercator
     * coordinates is to be converted - not a UTM coordinate. This is the
     * complementary function to the previous one. The function cannot
     * tell if a set of Northing/Easting coordinates are in the North
     * or South hemesphere - they just give distance from the equator not
     * direction - so only northern hemesphere lat/long coordinates are returned.
     * If you live south of the equator there is a note later in the code
     * explaining how to have it just return southern hemesphere lat/longs.
     *
     * @param long $LongOrigin
     * @author Brenor Brophy (brenorbrophy.com)
     * @link http://www.phpclasses.org/browse/file/10671.html
     */
    function convertTMtoLL($LongOrigin = NULL)
    {
        $k0 = 0.9996;
        $e1 = (1-sqrt(1-$this->e2))/(1+sqrt(1-$this->e2));
        $falseEasting = 0.0;
        $y = $this->utmNorthing;

        if (!$LongOrigin)
        { // It is a UTM coordinate we want to convert
            sscanf($this->utmZone,"%d%s",$ZoneNumber,$ZoneLetter);
            if ($ZoneLetter >= 'N')
            {
                $NorthernHemisphere = 1;//point is in northern hemisphere
            }
            else
            {
                $NorthernHemisphere = 0;//point is in southern hemisphere
                $y -= 10000000.0;//remove 10,000,000 meter offset used for southern hemisphere
            }
            $LongOrigin = ($ZoneNumber - 1)*6 - 180 + 3;  //+3 puts origin in middle of zone
            $falseEasting = 500000.0;
        }

//		$y -= 10000000.0;	// Uncomment line to make LOCAL coordinates return southern hemesphere Lat/Long
        $x = $this->utmEasting - $falseEasting; //remove 500,000 meter offset for longitude

        $eccPrimeSquared = ($this->e2)/(1-$this->e2);

        $M = $y / $k0;
        $mu = $M/($this->a*(1-$this->e2/4-3*$this->e2*$this->e2/64-5*$this->e2*$this->e2*$this->e2/256));

        $phi1Rad = $mu	+ (3*$e1/2-27*$e1*$e1*$e1/32)*sin(2*$mu)
            + (21*$e1*$e1/16-55*$e1*$e1*$e1*$e1/32)*sin(4*$mu)
            +(151*$e1*$e1*$e1/96)*sin(6*$mu);
        $phi1 = rad2deg($phi1Rad);

        $N1 = $this->a/sqrt(1-$this->e2*sin($phi1Rad)*sin($phi1Rad));
        $T1 = tan($phi1Rad)*tan($phi1Rad);
        $C1 = $eccPrimeSquared*cos($phi1Rad)*cos($phi1Rad);
        $R1 = $this->a*(1-$this->e2)/pow(1-$this->e2*sin($phi1Rad)*sin($phi1Rad), 1.5);
        $D = $x/($N1*$k0);

        $tlat = $phi1Rad - ($N1*tan($phi1Rad)/$R1)*($D*$D/2-(5+3*$T1+10*$C1-4*$C1*$C1-9*$eccPrimeSquared)*$D*$D*$D*$D/24
                +(61+90*$T1+298*$C1+45*$T1*$T1-252*$eccPrimeSquared-3*$C1*$C1)*$D*$D*$D*$D*$D*$D/720); // fixed in 1.1
        $this->lat = rad2deg($tlat);

        $tlong = ($D-(1+2*$T1+$C1)*$D*$D*$D/6+(5-2*$C1+28*$T1-3*$C1*$C1+8*$eccPrimeSquared+24*$T1*$T1)
                *$D*$D*$D*$D*$D/120)/cos($phi1Rad);
        $this->long = $LongOrigin + rad2deg($tlong);
    }
}
