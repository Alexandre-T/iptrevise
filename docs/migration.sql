select * from public.te_reseau_res;

select * from te_network;

insert into te_machine (mac_id, user_id, mac_lib, mac_des, mac_interface, mac_created, mac_updated)
select om.mac_id, nu.usr_id, om.mac_lib, om.mac_des, om.mac_interface, now(), now() from te_machine_mac as om
inner join public.user as ou on ou.id = om.usr_id
left OUTER JOIN ts_user as nu on nu.usr_mail = ou.email
;

alter TABLE te_tag ALTER COLUMN tag_id set default nextval('te_tag_tag_id_seq');

insert into te_tag (user_id, tag_lib,mac_created, mac_updated)
select distinct 1, substring(mac_type for 16), now(), now() from te_machine_mac
  as om
  inner join public.user as ou on ou.id = om.usr_id
  left OUTER JOIN ts_user as nu on nu.usr_mail = ou.email
;

alter TABLE te_tag ALTER COLUMN tag_id drop default;

insert into tj_machinetag (machine_id, tag_id)
select nm.mac_id, nt.tag_id from
  te_machine_mac as om
inner join te_machine as nm on nm.mac_lib = om.mac_lib
left outer join te_tag as nt on substring(nt.tag_lib for 16) = substring(om.mac_type for 16);

insert into ext_log_entries (id, action, logged_at, object_id, object_class, version, data, username)
    select nextval('ext_log_entries_id_seq'), 'create', now(), mac_id, 'App\Entity\Machine', 1,
      'a:3:{s:5:"label";s:' || char_length(mac_lib)  || ':"' || mac_lib ||'";s:11:"description";s:' || char_length(mac_des) || ':"' || mac_des || '";s:9:"interface";i:' || mac_interface || ';}',
      'Importateur automatique'
    from te_machine;

insert into te_ip (ip_id, ip_lib, network_id, machine_id, user_id, ip_ip, ip_created, ip_updated, reason)
select oi.ip_id, nr.net_id, oi.ip_lib, oi.mac_id, nu.usr_id, ip_adresse, now(), now(), substring(oi.ip_des for 32) FROM te_ip_ip as oi
inner join te_reseau_res as "or" on oi.res_id = "or".res_id
left outer join te_network as nr on "or".res_lib = nr.net_lib

  inner join public.user as ou on ou.id = oi.usr_id
  left OUTER JOIN ts_user as nu on nu.usr_mail = ou.email
where oi.res_id <> 3;

insert into ext_log_entries (id, action, logged_at, object_id, object_class, version, data, username)
  select nextval('ext_log_entries_id_seq'), 'create', now(), ip_id, 'App\Entity\Ip', 1,
    'a:4:{s:2:"ip";i:'|| ip_ip ||';s:6:"reason";s:' || char_length(reason) || ':"'|| reason ||'";s:7:"network";a:1:{s:2:"id";i:'|| network_id ||';}s:7:"machine";N;}',
    'Importateur automatique'
  from te_ip
where machine_id is null and reason is not null;

insert into ext_log_entries (id, action, logged_at, object_id, object_class, version, data, username)
  select nextval('ext_log_entries_id_seq'), 'create', now(), ip_id, 'App\Entity\Ip', 1,
    'a:4:{s:2:"ip";i:'|| ip_ip ||';s:6:"reason";s:' || char_length(reason) || ':"'|| reason ||'";s:7:"network";a:1:{s:2:"id";i:'|| network_id ||';}s:7:"machine";a:1:{s:2:"id";i:'|| machine_id || ';}}',
    'Importateur automatique'
  from te_ip
  where machine_id is not null and reason is not null;

select * from te_ip where reason <> ''
