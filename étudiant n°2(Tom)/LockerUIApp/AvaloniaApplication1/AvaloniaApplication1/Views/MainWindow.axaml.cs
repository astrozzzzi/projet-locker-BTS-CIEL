using System;
using System.Collections.Generic;
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

        private readonly List<string> lignesLogs = new List<string>();

        // Code local pour accéder au mode maintenance.
        // Pour la maquette, il est défini directement dans l'application.
        private const string CodeMaintenance = "wsuduma";

        private int casierActuel = -1;

        public MainWindow()
        {
            InitializeComponent();

            apiService = new ApiService();
            lockerController = new LockerController();

            btnValiderCode.Click += BtnValiderCode_Click;
            btnFermerCasier.Click += BtnFermerCasier_Click;
            btnTesterApi.Click += BtnTesterApi_Click;

            btnConnexionMaintenance.Click += BtnConnexionMaintenance_Click;
            btnDeconnexionMaintenance.Click += BtnDeconnexionMaintenance_Click;

            btnTestDeverrouiller.Click += BtnTestDeverrouiller_Click;
            btnTestVerrouiller.Click += BtnTestVerrouiller_Click;

            AjouterLog("Application démarrée");
        }

        private async void BtnTesterApi_Click(object? sender, RoutedEventArgs e)
        {
            string resultat = await apiService.TesterApiAsync();

            if (resultat.StartsWith("API accessible"))
            {
                AjouterLog("API accessible");
            }
            else
            {
                AjouterLog("Erreur API");
            }
        }

        private async void BtnValiderCode_Click(object? sender, RoutedEventArgs e)
        {
            string code = txtCode.Text?.Trim() ?? "";

            if (code == "")
            {
                AjouterLog("Veuillez saisir un code");
                txtCasier.Text = "Aucun casier sélectionné";
                return;
            }

            Colis? colis = await apiService.RechercherColisAsync(code);

            if (colis == null)
            {
                AjouterLog("Code invalide");
                txtCasier.Text = "Aucun casier sélectionné";
                return;
            }

            casierActuel = RecupererCasier(colis);

            txtCasier.Text = $"Casier n°{casierActuel}";

            AjouterLog("Code valide");

            string resultat = lockerController.OuvrirCasier(casierActuel);

            AjouterLog(resultat);
        }

        private void BtnFermerCasier_Click(object? sender, RoutedEventArgs e)
        {
            if (casierActuel == -1)
            {
                AjouterLog("Aucun casier ouvert");
                return;
            }

            string resultat = lockerController.FermerCasier(casierActuel);

            AjouterLog(resultat);

            txtCasier.Text = "Aucun casier sélectionné";
            casierActuel = -1;
        }

        private void BtnConnexionMaintenance_Click(object? sender, RoutedEventArgs e)
        {
            string code = txtMotDePasseMaintenance.Text?.Trim() ?? "";

            if (code == CodeMaintenance)
            {
                panelMaintenance.IsVisible = true;
                btnDeconnexionMaintenance.IsVisible = true;
                txtMotDePasseMaintenance.Text = "";

                AjouterLog("Mode maintenance activé");
            }
            else
            {
                panelMaintenance.IsVisible = false;
                btnDeconnexionMaintenance.IsVisible = false;

                AjouterLog("Code maintenance incorrect");
            }
        }

        private void BtnDeconnexionMaintenance_Click(object? sender, RoutedEventArgs e)
        {
            panelMaintenance.IsVisible = false;
            btnDeconnexionMaintenance.IsVisible = false;
            txtMotDePasseMaintenance.Text = "";

            AjouterLog("Mode maintenance désactivé");
        }

        private int RecupererCasierTest()
        {
            return comboCasierTest.SelectedIndex + 1;
        }

        private void BtnTestDeverrouiller_Click(object? sender, RoutedEventArgs e)
        {
            int idCasier = RecupererCasierTest();

            string resultat = lockerController.OuvrirCasier(idCasier);

            txtCasier.Text = $"Casier n°{idCasier}";

            AjouterLog($"Test : casier n°{idCasier} déverrouillé");
        }

        private void BtnTestVerrouiller_Click(object? sender, RoutedEventArgs e)
        {
            int idCasier = RecupererCasierTest();

            string resultat = lockerController.FermerCasier(idCasier);

            txtCasier.Text = $"Casier n°{idCasier}";

            AjouterLog($"Test : casier n°{idCasier} verrouillé");
        }

        private int RecupererCasier(Colis colis)
        {
            if (colis.CasierIdCasier.HasValue)
            {
                return colis.CasierIdCasier.Value;
            }

            AjouterLog("Casier non défini, casier n°1 utilisé");
            return 1;
        }

        private void AjouterLog(string message)
        {
            lignesLogs.Add(message);

            if (lignesLogs.Count > 6)
            {
                lignesLogs.RemoveAt(0);
            }

            txtLogs.Text = string.Join(Environment.NewLine, lignesLogs);
            txtLogs.CaretIndex = txtLogs.Text.Length;
        }
    }
}