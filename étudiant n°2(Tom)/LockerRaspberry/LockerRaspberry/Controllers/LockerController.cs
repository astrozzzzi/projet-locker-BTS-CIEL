using System;

namespace LockerRaspberry.Controllers
{
    public class LockerController
    {
        public void OuvrirCasier(int idCasier)
        {
            // Simulation pour l’instant
            // Plus tard : code GPIO Raspberry Pi ici

            Console.WriteLine($"Ouverture du casier {idCasier}");
        }

        public void FermerCasier(int idCasier)
        {
            // Simulation pour l’instant
            // Plus tard : code GPIO Raspberry Pi ici

            Console.WriteLine($"Fermeture du casier {idCasier}");
        }
    }
}