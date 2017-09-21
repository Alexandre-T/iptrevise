TODO List
=========
* Dans la vue des IPs, préciser qu'on ne supprime que l'adresse IP.
* Vérifié les assertions dans le formulaire sur ip+machine
* Consulter sensiolabs
* SHOW.HTML.TWIG Le coeur des vues machines et network doit être exporté dans une vue externe pour ne pas réécrire le même code dans la vue IP
* Text de la home page
* Etat du taux d'occupation des réseaux si on est connecté
* Nombre de machines déclarées
* Nombres d'IP déclarées
* Renommer référencé par réservée
* Trier le tableau dans show network
* Créer un Réseau : vérifier la validité de l'adresse réseau avec un validator de type callback. 
* Retirer les hacks pour ajouter des icones par javascript et utiliser la clef icon comme dans le fichier NetworkController::createLinkForm
* Ajouter les droits sur les boutons icones : {% if is_granted("ROLE") %} Hi {{ app.user.username }} {% endif %}
* Ajouter la classe aux tables le nécessitant  
    *  striped : <table class="table table-striped">
    *  table-bordered
    *  .table-hover
    *  .table-condensed
* Use case : ajouter une IP à une machine
* Remplacer les {% block headline %}{% endblock %} par des {% block page_header %}{% endblock %}
* Remplacer les $this->get(IpManager::class) par un $this->get('ip_manager')
* Remplacer les $this->get(MachineManager::class) par un $this->get('machine_manager')
* Remplacer les $this->get(NetworkManager::class) par un $this->get('network_manager')
* Remplacer les $this->get(UserManager::class) par un $this->get('user_manager')
* Ajouter dans les formulaires des champs non remplaçables (réservé par...)
* Ajouter les tags selon le lien Symfony donné
* Ajouter le graphisme des tgs selon ce lien https://github.com/aehlke/tag-it
* Utiliser des tata-tables de sb-admin-2 à la place du paginator
