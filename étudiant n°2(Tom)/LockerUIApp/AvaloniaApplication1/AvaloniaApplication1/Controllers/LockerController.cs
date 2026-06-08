using System;
using System.Collections.Generic;
using System.Device.Gpio;
using System.Runtime.InteropServices;

namespace AvaloniaLockerApp.Controllers
{
    public class LockerController : IDisposable
    {
        private GpioController? gpioController;
        private readonly bool gpioDisponible;

        // GPIO utilisés pour chaque casier
        // À adapter selon ton câblage réel
        private readonly Dictionary<int, int> pinsCasiers = new Dictionary<int, int>
        {
            { 1, 17 }, // Casier 1 -> GPIO 17
            { 2, 27 }, // Casier 2 -> GPIO 27
            { 3, 22 }, // Casier 3 -> GPIO 22
            { 4, 23 }  // Casier 4 -> GPIO 23
        };

        // À modifier après test si ton module fonctionne à l'envers
        private readonly PinValue aimantActive = PinValue.High;
        private readonly PinValue aimantDesactive = PinValue.Low;

        public LockerController()
        {
            gpioDisponible = RuntimeInformation.IsOSPlatform(OSPlatform.Linux);

            if (gpioDisponible)
            {
                gpioController = new GpioController();

                foreach (int pin in pinsCasiers.Values)
                {
                    gpioController.OpenPin(pin, PinMode.Output);

                    // Au démarrage, on verrouille les casiers
                    gpioController.Write(pin, aimantActive);
                }
            }
        }

        public string OuvrirCasier(int idCasier)
        {
            if (!pinsCasiers.ContainsKey(idCasier))
            {
                return $"Erreur : casier {idCasier} inconnu.";
            }

            int pin = pinsCasiers[idCasier];

            if (!gpioDisponible || gpioController == null)
            {
                return $"Simulation : désactivation de l'électroaimant du casier {idCasier} sur GPIO {pin}.";
            }

            // Ouvrir = libérer la porte = désactiver l'électroaimant
            gpioController.Write(pin, aimantDesactive);

            return $"Casier {idCasier} déverrouillé via GPIO {pin}.";
        }

        public string FermerCasier(int idCasier)
        {
            if (!pinsCasiers.ContainsKey(idCasier))
            {
                return $"Erreur : casier {idCasier} inconnu.";
            }

            int pin = pinsCasiers[idCasier];

            if (!gpioDisponible || gpioController == null)
            {
                return $"Simulation : activation de l'électroaimant du casier {idCasier} sur GPIO {pin}.";
            }

            // Fermer = verrouiller la porte = activer l'électroaimant
            gpioController.Write(pin, aimantActive);

            return $"Casier {idCasier} verrouillé via GPIO {pin}.";
        }

        public void Dispose()
        {
            if (gpioController != null)
            {
                foreach (int pin in pinsCasiers.Values)
                {
                    if (gpioController.IsPinOpen(pin))
                    {
                        gpioController.Write(pin, aimantDesactive);
                        gpioController.ClosePin(pin);
                    }
                }

                gpioController.Dispose();
            }
        }
    }
}