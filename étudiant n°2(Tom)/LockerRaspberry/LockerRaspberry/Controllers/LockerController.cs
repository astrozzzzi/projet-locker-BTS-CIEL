using System;

namespace LockerRaspberry.Controllers
{
    public class LockerController
    {
        public void OuvrirCasier(int idCasier)
        {
            // Pour l’instant simulation
            Console.WriteLine($"Ouverture du casier {idCasier}");

            // Plus tard : code GPIO Raspberry Pi ici
        }

        public void FermerCasier(int idCasier)
        {
            // Pour l’instant simulation
            Console.WriteLine($"Fermeture du casier {idCasier}");

            // Plus tard : code GPIO Raspberry Pi ici
        }
    }
}