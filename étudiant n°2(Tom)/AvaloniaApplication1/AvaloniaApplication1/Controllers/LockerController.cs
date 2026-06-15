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

        private readonly Dictionary<int, int> pinsCasiers = new Dictionary<int, int>
        {
            { 1, 5 },   // Casier 1 -> port D5
            { 2, 12 },  // Casier 2 -> port D12
            { 3, 16 },  // Casier 3 -> port D16
            { 4, 15 }   // Casier 4 -> port UART RX
        };

        // État normal : électroaimant actif = casier verrouillé
        // Si les électroaimants fonctionnent à l'envers, inverse High et Low.
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

                        // Au démarrage, tous les casiers sont verrouillés
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

            // Déverrouiller = désactiver l'électroaimant
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

            // Verrouiller = activer l'électroaimant
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