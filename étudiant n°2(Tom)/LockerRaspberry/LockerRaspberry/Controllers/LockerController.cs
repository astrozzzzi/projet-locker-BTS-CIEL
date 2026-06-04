using System;

namespace LockerRaspberry.Controllers
{
    public class LockerController
    {
        public void OuvrirCasier(int idCasier)
        {
            Console.WriteLine($"Ouverture du casier {idCasier}");
        }

        public void FermerCasier(int idCasier)
        {
            Console.WriteLine($"Fermeture du casier {idCasier}");
        }
    }
}