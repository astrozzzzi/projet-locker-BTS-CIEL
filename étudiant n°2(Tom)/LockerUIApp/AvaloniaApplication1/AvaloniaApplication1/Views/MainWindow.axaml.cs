using System;
using Avalonia.Controls;
using Avalonia.Interactivity;
using AvaloniaLockerApp.Controllers;
using AvaloniaLockerApp.Models;
using AvaloniaLockerApp.Services;

namespace AvaloniaLockerApp.Views
{
    public partial class MainWindow : Window
    {
        private readonly ApiService apiService;
        private readonly LockerController lockerController;

        private int casierActuel = -1;

        public MainWindow()
        {
            InitializeComponent();

            apiService = new ApiService();
            lockerController = new LockerController();

            btnValiderCode.Click += BtnValiderCode_Click;
            btnFermerCasier.Click += BtnFermerCasier_Click;
            btnTesterApi.Click += BtnTesterApi_Click;

            AjouterLog("Application Locker Raspberry démarrée.");
        }

        private async void BtnTesterApi_Click(object? sender, RoutedEventArgs e)
        {
            AjouterLog("Test de connexion à l'API...");

            string resultat = await apiService.TesterApiAsync();

            AjouterLog(resultat);
        }

        private async void BtnValiderCode_Click(object? sender, RoutedEventArgs e)
        {
            string numeroColis = txtCode.Text?.Trim() ?? "";

            if (numeroColis == "")
            {
                AjouterLog("Veuillez saisir un numéro de colis.");
                return;
            }

            AjouterLog($"Recherche du colis {numeroColis}...");

            Colis? colis = await apiService.RechercherColisAsync(numeroColis);

            if (colis == null)
            {
                AjouterLog("Colis introuvable ou erreur API.");
                return;
            }

            casierActuel = RecupererCasier(colis);

            AjouterLog($"Colis trouvé : {colis.NumColis}");
            AjouterLog($"Casier associé : {casierActuel}");

            string resultat = lockerController.OuvrirCasier(casierActuel);

            AjouterLog(resultat);
        }

        private void BtnFermerCasier_Click(object? sender, RoutedEventArgs e)
        {
            if (casierActuel == -1)
            {
                AjouterLog("Aucun casier ouvert.");
                return;
            }

            string resultat = lockerController.FermerCasier(casierActuel);

            AjouterLog(resultat);

            casierActuel = -1;
        }

        private int RecupererCasier(Colis colis)
        {
            if (colis.CasierIdCasier.HasValue)
            {
                return colis.CasierIdCasier.Value;
            }

            AjouterLog("Aucun casier associé par l'API. Casier 1 utilisé pour test.");
            return 1;
        }

        private void AjouterLog(string message)
        {
            string ligne = $"[{DateTime.Now:HH:mm:ss}] {message}";

            txtLogs.Text += ligne + Environment.NewLine;
            txtLogs.CaretIndex = txtLogs.Text.Length;
        }
    }
}