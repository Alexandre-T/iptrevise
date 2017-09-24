TODO List
=========
* Ajouter des onglets dans l'administration des utilisateurs
* Dans les panel de statistiques, changer le lien détails par un lien vers le futur module de statistiques.
* Etat du taux d'occupation des réseaux si on est connecté
* Créer un Réseau : vérifier la validité de l'adresse réseau avec un validator de type callback. 
* Retirer les hacks pour ajouter des icones par javascript et utiliser la clef icon comme dans le fichier NetworkController::createLinkForm
* Ajouter les droits sur les boutons icones : {% if is_granted("ROLE") %} Hi {{ app.user.username }} {% endif %}
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
* Ajouter le graphisme des tgs selon ce lien https://github.com/aehlke/tag-it
* Vérifier les assertions dans le formulaire sur ip+machine
* Consulter sensiolabs
