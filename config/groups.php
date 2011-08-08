<?php

$access = array(
	0 => 'Private',
	1 => 'All feide users can read, no anonymous access',
	2 => 'Anonymous users can read',
	3 => 'Feide users can write, no anonymous access',
	4 => 'Feide users can write, anonymous users can read'
);

$groups = array(
	'@realm-uninett.no'	=> 'Everyone at UNINETT',
	'@affiliation-uninett.no-employee' => 'Employees at UNINETT',
	'@affiliation-uninett.no-member' => 'Members of UNINETT',

	/* Group values from UNINETT OrgUnitDN. From LDAP crawler. */
	'@orgunit-uninett.no-ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no'       => 'UNINETT Tjenesteavdelingen',
	'@orgunit-uninett.no-ou=NA_ou=UNINETT_ou=organization_dc=uninett_dc=no'       => 'UNINETT Nettavdelingen',
	'@orgunit-uninett.no-ou=UNINETT_Norid_ou=organization_dc=uninett_dc=no'       => 'UNINETT Norid',
	'@orgunit-uninett.no-ou=UNINETT_FAS_ou=organization_dc=uninett_dc=no'         => 'UNINETT FAS',
	'@orgunit-uninett.no-ou=ADM_ou=UNINETT_ou=organization_dc=uninett_dc=no'      => 'UNINETT Administrasjon',
	'@orgunit-uninett.no-ou=UNINETT_ABC_ou=organization_dc=uninett_dc=no'         => 'UNINETT ABC',
	'@orgunit-uninett.no-ou=FOU_ou=UNINETT_ou=organization_dc=uninett_dc=no'      => 'UNINETT FoU',
	'@orgunit-uninett.no-ou=CN_ou=NA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Campusnett gruppe',
	'@orgunit-uninett.no-ou=TN_ou=NA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Transportnett gruppe',
	'@orgunit-uninett.no-ou=ET_ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Eksterne tjenester gruppe',
	'@orgunit-uninett.no-ou=SD_ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Systemdrift gruppe',
	'@orgunit-uninett.no-ou=SU_ou=TA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Systemutvikling gruppe',
	'@orgunit-uninett.no-ou=UNINETT_Sigma_ou=organization_dc=uninett_dc=no'       => 'UNINETT Sigma',
	'@orgunit-uninett.no-ou=SI_ou=NA_ou=UNINETT_ou=organization_dc=uninett_dc=no' => 'UNINETT Sikkerhet gruppe',
	
	'@realm-hit.no' => 'HiT: Alle brukere',
	'@orgunit-hit.no-ou=BIB_ou=Organisasjon_o=HIT' => 'HiT: Alle ved biblioteket',

	
	'@feidecore' => 'Feide prosjektgruppe',
	'@realm-uio.no'	=> 'Everyone at UiO',
	'@realm-ntnu.no'=> 'Everyone at NTNU',
	'@realm-uit.no'	=> 'Everyone at UiT',
	'@realm-uib.no'	=> 'Everyone at UiB',
	'@entitlement-orphanage.dk_aai.dk-dk.dk_aai.orphanage.dev' => 'DK-AAI Utviklingsgruppe'
);