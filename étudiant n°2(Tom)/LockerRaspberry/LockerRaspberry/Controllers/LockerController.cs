using System;

public class LockerController
{
    public event Action<string>? OnLog;

    public void OuvrirCasier(int numeroCasier)
    {
        OnLog?.Invoke($"Ouverture du casier {numeroCasier}");

        // Ici tu mettras plus tard le code GPIO Raspberry Pi
        // Exemple : activer électro-aimant

        OnLog?.Invoke($"Casier {numeroCasier} ouvert");
    }

    public void FermerCasier(int numeroCasier)
    {
        OnLog?.Invoke($"Fermeture du casier {numeroCasier}");

        // Ici tu mettras plus tard le code GPIO Raspberry Pi

        OnLog?.Invoke($"Casier {numeroCasier} fermé");
    }
}