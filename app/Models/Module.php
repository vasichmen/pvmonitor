<?php


namespace App\Models;

class Module extends AbstractModel
{
    protected $fillable = [
        'name',
        'manufacturer',
        'technology',
        'bifacial',
        'stc',
        'ptc',
        'a_c',
        'length',
        'width',
        'n_s',
        'i_sc_ref',
        'v_oc_ref',
        'i_mp_ref',
        'v_mp_ref',
        'alpha_sc',
        'beta_oc',
        't_noct',
        'a_ref',
        'i_l_ref',
        'i_o_ref',
        'r_s',
        'r_sh_ref',
        'adjust',
        'gamma_r',
        'bipv',
        'version',
        'date',
    ];


}
