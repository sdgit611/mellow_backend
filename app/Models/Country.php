<?php

namespace App\Models;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $iso
 * @property string $name
 * @property string $nicename
 * @property string|null $iso3
 * @property int|null $numcode
 * @property int $phonecode
 * @method static \Illuminate\Database\Eloquent\Builder|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereIso3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNicename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country whereNumcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Country wherePhonecode($value)
 * @property-read mixed $nationality
 * @mixin \Eloquent
 */
class Country extends BaseModel
{

    protected $appends = ['nationality'];
    public $timestamps = false;

    const NATIONALITY = array(
        'AF' => 'Afghan',
        'AL' => 'Albanian',
        'DZ' => 'Algerian',
        'AS' => 'American Samoans',
        'AD' => 'Andorran',
        'AO' => 'Angolan',
        'AG' => 'Antiguans',
        'AR' => 'Argentinean',
        'AM' => 'Armenian',
        'AU' => 'Australian',
        'AT' => 'Austrian',
        'AZ' => 'Azerbaijani',
        'BS' => 'Bahamian',
        'BH' => 'Bahraini',
        'BD' => 'Bangladeshi',
        'BB' => 'Barbadian',
        'BY' => 'Belarusian',
        'BE' => 'Belgian',
        'BZ' => 'Belizean',
        'BJ' => 'Beninese',
        'BT' => 'Bhutanese',
        'BO' => 'Bolivian',
        'BA' => 'Bosnian',
        'BR' => 'Brazilian',
        'BN' => 'Bruneian',
        'BG' => 'Bulgarian',
        'BF' => 'Burkinabe',
        'BI' => 'Burundian',
        'KH' => 'Cambodian',
        'CM' => 'Cameroonian',
        'CA' => 'Canadian',
        'CV' => 'Cape Verdean',
        'CF' => 'Central African',
        'TD' => 'Chadian',
        'CL' => 'Chilean',
        'CN' => 'Chinese',
        'CO' => 'Colombian',
        'KM' => 'Comoran',
        'CG' => 'Congolese',
        'CR' => 'Costa Rican',
        'HR' => 'Croatian',
        'CU' => 'Cuban',
        'CY' => 'Cypriot',
        'CZ' => 'Czech',
        'DK' => 'Danish',
        'DJ' => 'Djibouti',
        'DM' => 'Dominican',
        'dutch' => 'Dutch',
        'EC' => 'Ecuadorean',
        'EG' => 'Egyptian',
        'emirian' => 'Emirian',
        'GQ' => 'Equatorial Guinean',
        'ER' => 'Eritrean',
        'EE' => 'Estonian',
        'ET' => 'Ethiopian',
        'FJ' => 'Fijian',
        'filipino' => 'Filipino',
        'FI' => 'Finn   ish',
        'FR' => 'French',
        'GA' => 'Gabonese',
        'GM' => 'Gambian',
        'GE' => 'Georgian',
        'DE' => 'German',
        'GH' => 'Ghanaian',
        'GR' => 'Greek',
        'GD' => 'Grenadian',
        'GT' => 'Guatemalan',
        'GW' => 'Guinean-Bissau',
        'GN' => 'Guinean',
        'GY' => 'Guyanese',
        'HT' => 'Haitian',
        'HN' => 'Honduran',
        'HU' => 'Hungarian',
        'IS' => 'Icelander',
        'IN' => 'Indian',
        'ID' => 'Indonesian',
        'IR' => 'Iranian',
        'IQ' => 'Iraqi',
        'IE' => 'Irish',
        'IL' => 'Israeli',
        'IT' => 'Italian',
        'JM' => 'Jamaican',
        'JP' => 'Japanese',
        'JO' => 'Jordanian',
        'KZ' => 'Kazakhstani',
        'KE' => 'Kenyan',
        'KW' => 'Kuwaiti',
        'KG' => 'Kyrgyz',
        'LA' => 'Laotian',
        'LV' => 'Latvian',
        'LB' => 'Lebanese',
        'LR' => 'Liberian',
        'LY' => 'Libyan',
        'LI' => 'Liechtensteiner',
        'LT' => 'Lithuanian',
        'LU' => 'Luxembourger',
        'Mk' => 'Macedonian',
        'MG' => 'Malagasy',
        'MW' => 'Malawian',
        'MY' => 'Malaysian',
        'MV' => 'Maldivan',
        'ML' => 'Malian',
        'MT' => 'Maltese',
        'MH' => 'Marshallese',
        'MR' => 'Mauritanian',
        'MU' => 'Mauritian',
        'MX' => 'Mexican',
        'FM' => 'Micronesian',
        'MD' => 'Moldovan',
        'MC' => 'Monacan',
        'MN' => 'Mongolian',
        'MA' => 'Moroccan',
        'MZ' => 'Mozambican',
        'NA' => 'Namibian',
        'NR' => 'Nauruan',
        'NP' => 'Nepalese',
        'NZ' => 'New Zealander',
        'NI' => 'Nicaraguan',
        'NG' => 'Nigerien',
        'north korean' => 'North Korean',
        'NO' => 'Norwegian',
        'OM' => 'Omani',
        'PK' => 'Pakistani',
        'PW' => 'Palauan',
        'PS' => 'Palestinian',
        'PA' => 'Panamanian',
        'PG' => 'Papua New Guinean',
        'PY' => 'Paraguayan',
        'PE' => 'Peruvian',
        'PL' => 'Polish',
        'PT' => 'Portuguese',
        'QR' => 'Qatari',
        'RO' => 'Romanian',
        'RU' => 'Russian',
        'RW' => 'Rwandan',
        'LC' => 'Saint Lucian',
        'salvadoran' => 'Salvadoran',
        'WS' => 'Samoan',
        'SM' => 'San Marinese',
        'ST' => 'Sao Tomean',
        'SA' => 'Saudi',
        'SN' => 'Senegalese',
        'CS' => 'Serbian',
        'SC' => 'Seychellois',
        'SL' => 'Sierra Leonean',
        'SG' => 'Singaporean',
        'SK' => 'Slovakian',
        'SI' => 'Slovenian',
        'SB' => 'Solomon Islander',
        'SO' => 'Somali',
        'ZA' => 'South African',
        'ES' => 'Spanish',
        'LK' => 'Sri Lankan',
        'SD' => 'Sudanese',
        'SR' => 'Surinamer',
        'SZ' => 'Swazi',
        'SE' => 'Swedish',
        'CH' => 'Swiss',
        'SY' => 'Syrian',
        'TW' => 'Taiwanese',
        'TJ' => 'Tajik',
        'TZ' => 'Tanzanian',
        'TH' => 'Thai',
        'TG' => 'Togolese',
        'TO' => 'Tongan',
        'TT' => 'Trinidadian or Tobagonian',
        'TN' => 'Tunisian',
        'TR' => 'Turkish',
        'TV' => 'Tuvaluan',
        'UG' => 'Ugandan',
        'UA' => 'Ukrainian',
        'uruguayan' => 'Uruguayan',
        'uzbekistani' => 'Uzbekistani',
        'VE' => 'Venezuelan',
        'VN' => 'Vietnamese',
        'welsh' => 'Welsh',
        'YE' => 'Yemenite',
        'ZM' => 'Zambian',
        'ZW' => 'Zimbabwean',
        'AI' => 'Anguillian',
        'AW' => 'Aruban',
        'BM' => 'Bermudian',
        'BW' => 'Motswana',
        'CC' => 'Cocos Islander',
        'CD' => 'Congolese',
        'CK' => 'Cook Islander',
        'CI' => 'Ivorian',
        'DO' => 'Dominican',
        'SV' => 'Salvadoran',
        'FO' => 'Faroese',
        'PF' => 'French Polynesian',
        'TF' => 'French',
        'GL' => 'Greenlandic',
        'GP' => 'Guadeloupian',
        'GU' => 'Guamanian',
        'HM' => 'Heard and McDonald Islander',
        'HK' => 'Chinese',
        'KI' => 'I-Kiribati',
        'KP' => 'North Korean',
        'KR' => 'South Korean',
        'LS' => 'Mosotho',
        'MO' => 'Chinese',
        'MQ' => 'French',
        'YT' => 'French',
        'MS' => 'Montserratian',
        'MM' => 'Myanmar',
        'NL' => 'Dutch',
        'AN' => 'Dutch',
        'NC' => 'New Caledonian',
        'NE' => 'Nigerian',
        'NU' => 'Niuean',
        'NF' => 'Norfolk Islander',
        'MP' => 'American',
        'PH' => 'Filipino',
        'PN' => 'Pitcairn Islander',
        'PR' => 'Puerto Rican',
        'RE' => 'French',
        'SH' => 'Saint Helenian',
        'KN' => 'Kittian and Nevisian',
        'PM' => 'French',
        'VC' => 'Saint Vincentian',
        'GS' => 'South Georgia and the South Georgia Islander',
        'SJ' => 'Norwegian',
        'TL' => 'East Timorese',
        'TK' => 'Tokelauan',
        'TM' => 'Turkmen',
        'TC' => 'Turks and Caicos Islander',
        'AE' => 'Emirati',
        'US' => 'American',
        'GB' => 'British',
        'UM' => 'American',
        'VU' => 'Ni-Vanuatu',
        'VG' => 'Virgin Islander',
        'VI' => 'Virgin Islander',
        'WF' => 'Walls and Futuna Islander',
        'EH' => 'Sahrawi',
        'ME' => 'Montenegrin',
        'AX' => 'Swedish',
        'CW' => 'Curacaoan',
        'GG' => 'Channel Islander',
        'IM' => 'Manx',
        'JE' => 'Channel Islander',
        'BL' => 'Saint Barthélemy Islander',
        'MF' => 'Saint Martin Islander'
    );

    public function getNationalityAttribute()
    {
        return Country::NATIONALITY[$this->iso] ?? 'unknown';
    }

    public function flagSpanCountryCode()
    {
        return '<span class="flag-icon  flag-icon-squared flag-icon-' . strtolower($this->iso) . '"></span> +' . $this->phonecode;
    }

}
