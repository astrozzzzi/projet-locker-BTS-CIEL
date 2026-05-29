using System;
using System.Collections.Generic;
using System.Text;

namespace LockerRaspberry.Models
{
    public class Colis
    {
        public int idColis { get; set; }
        public int num_colis { get; set; }
        public float longueur { get; set; }
        public float largeur { get; set; }
        public float hauteur { get; set; }
        public int Livreur_idLivreur { get; set; }
        public int Casier_idCasier { get; set; }
        public int Clients_idExpediteur { get; set; }
        public int Clients_idDestinataire { get; set; }
    }
}