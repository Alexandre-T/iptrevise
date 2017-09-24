TODO List
=========
* Si l'entité n'est pas effaçable, ne pas créer le formulaire de suppression !
* Ajouter les droits sur les boutons icones : {% if is_granted("ROLE") %} .... {% endif %}
* Créer des tests avec le rôle reader.
* Dans les panel de statistiques, changer le lien détails par un lien vers le futur module de statistiques.
* Etat du taux d'occupation des réseaux si on est connecté
* Utiliser des tata-tables de sb-admin-2 à la place du paginator
* Ajouter la classe aux tables le nécessitant  
    *  striped : <table class="table table-striped">
    *  table-bordered
    *  .table-hover
    *  .table-condensed
* Remplacer les $this->get(IpManager::class) par un $this->get('ip_manager')
* Remplacer les $this->get(MachineManager::class) par un $this->get('machine_manager')
* Remplacer les $this->get(NetworkManager::class) par un $this->get('network_manager')
* Remplacer les $this->get(UserManager::class) par un $this->get('user_manager')
* Ajouter dans les formulaires des champs non remplaçables (réservé par...)
* Ajouter l'ergonomie des tags selon ce lien https://github.com/aehlke/tag-it
* Vérifier les assertions dans le formulaire sur ip+machine
* Consulter sensiolabs
