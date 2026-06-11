using System;
using System.Collections.Generic;
using System.Device.Gpio;
using System.Runtime.InteropServices;

namespace AvaloniaLockerApp.Controllers
{
    public class LockerController : IDisposable
    {
        private GpioController? gpioController;
        private bool gpioDisponible;

        // Branchement réel sur le Grove Base HAT
        private readonly Dictionary<int, int> pinsCasiers = new Dictionary<int, int>
        {
            { 1, 5 },   // Casier 1 -> D5
            { 2, 12 },  // Casier 2 -> D12
            { 3, 16 },  // Casier 3 -> D16
            { 4, 15 }   // Casier 4 -> UART RX
        };

        // Si le fonctionnement est inversé pendant les tests,
        // il suffit d'inverser ces deux valeurs.
        private readonly PinValue aimantActive = PinValue.High;
        private readonly PinValue aimantDesactive = PinValue.Low;

        public LockerController()
        {
            gpioDisponible = RuntimeInformation.IsOSPlatform(OSPlatform.Linux);

            if (gpioDisponible)
            {
                try
                {
                    gpioController = new GpioController();

                    foreach (int pin in pinsCasiers.Values)
                    {
                        gpioController.OpenPin(pin, PinMode.Output);

                        // Au démarrage, les casiers sont verrouillés
                        gpioController.Write(pin, aimantActive);
                    }
                }
                catch
                {
                    gpioDisponible = false;
                    gpioController = null;
                }
            }
        }

        public string OuvrirCasier(int idCasier)
        {
            if (!pinsCasiers.ContainsKey(idCasier))
            {
                return $"Casier {idCasier} inconnu";
            }

            int pin = pinsCasiers[idCasier];

            if (!gpioDisponible || gpioController == null)
            {
                return $"Simulation : casier n°{idCasier} déverrouillé";
            }

            // Ouvrir = déverrouiller = désactiver l'électroaimant
            gpioController.Write(pin, aimantDesactive);

            return $"Casier n°{idCasier} déverrouillé";
        }

        public string FermerCasier(int idCasier)
        {
            if (!pinsCasiers.ContainsKey(idCasier))
            {
                return $"Casier {idCasier} inconnu";
            }

            int pin = pinsCasiers[idCasier];

            if (!gpioDisponible || gpioController == null)
            {
                return $"Simulation : casier n°{idCasier} verrouillé";
            }

            // Fermer = verrouiller = activer l'électroaimant
            gpioController.Write(pin, aimantActive);

            return $"Casier n°{idCasier} verrouillé";
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