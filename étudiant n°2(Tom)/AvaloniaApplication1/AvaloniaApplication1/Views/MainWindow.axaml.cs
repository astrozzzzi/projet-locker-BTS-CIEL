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
        private const string CodeMaintenance = "wsuduma";

        private int casierActuel = -1;

        public MainWindow()
        {
            InitializeComponent();

            apiService = new ApiService();
            lockerController = new LockerController();

            btnValiderCode.Click += BtnValiderCode_Click;
            btnFermerCasier.Click += BtnFermerCasier_Click;

            btnConnexionMaintenance.Click += BtnConnexionMaintenance_Click;
            btnDeconnexionMaintenance.Click += BtnDeconnexionMaintenance_Click;

            btnTestDeverrouiller.Click += BtnTestDeverrouiller_Click;
            btnTestVerrouiller.Click += BtnTestVerrouiller_Click;

            MettreAJourEtat("En attente d'un code");
            AjouterLog("Application démarrée");
        }

        private async void BtnValiderCode_Click(object? sender, RoutedEventArgs e)
        {
            string code = txtCode.Text?.Trim() ?? "";

            if (code == "")
            {
                txtCasier.Text = "Aucun casier sélectionné";
                MettreAJourEtat("Veuillez saisir un code");
                AjouterLog("Code vide");
                return;
            }

            txtCasier.Text = "Aucun casier sélectionné";
            MettreAJourEtat("Vérification du code...");
            AjouterLog("Vérification du code");

            Colis? colis = await apiService.RechercherColisAsync(code);

            if (colis == null)
            {
                txtCasier.Text = "Aucun casier sélectionné";
                MettreAJourEtat("Code invalide ou API inaccessible");
                AjouterLog("Code invalide ou API inaccessible");
                return;
            }

            casierActuel = RecupererCasierDepuisCode(colis);

            if (casierActuel == -1)
            {
                txtCasier.Text = "Aucun casier sélectionné";
                MettreAJourEtat("Code invalide : casier inconnu");
                AjouterLog("Casier introuvable depuis code_colis");
                return;
            }

            txtCasier.Text = $"Casier n°{casierActuel}";
            MettreAJourEtat($"Casier n°{casierActuel} sélectionné");

            AjouterLog($"Code valide - casier n°{casierActuel}");

            string resultat = lockerController.OuvrirCasier(casierActuel);

            MettreAJourEtat($"Casier n°{casierActuel} déverrouillé");
            AjouterLog(resultat);
        }

        private void BtnFermerCasier_Click(object? sender, RoutedEventArgs e)
        {
            if (casierActuel == -1)
            {
                txtCasier.Text = "Aucun casier sélectionné";
                MettreAJourEtat("Aucun casier à verrouiller");
                AjouterLog("Aucun casier ouvert");
                return;
            }

            string resultat = lockerController.FermerCasier(casierActuel);

            MettreAJourEtat($"Casier n°{casierActuel} verrouillé");
            AjouterLog(resultat);

            txtCasier.Text = "Aucun casier sélectionné";
            txtCode.Text = "";
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

                MettreAJourEtat("Mode maintenance activé");
                AjouterLog("Mode maintenance activé");
            }
            else
            {
                panelMaintenance.IsVisible = false;
                btnDeconnexionMaintenance.IsVisible = false;

                MettreAJourEtat("Code maintenance incorrect");
                AjouterLog("Code maintenance incorrect");
            }
        }

        private void BtnDeconnexionMaintenance_Click(object? sender, RoutedEventArgs e)
        {
            panelMaintenance.IsVisible = false;
            btnDeconnexionMaintenance.IsVisible = false;
            txtMotDePasseMaintenance.Text = "";

            MettreAJourEtat("Mode maintenance désactivé");
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

            casierActuel = idCasier;
            txtCasier.Text = $"Casier n°{idCasier}";
            MettreAJourEtat($"Casier n°{idCasier} déverrouillé");

            AjouterLog($"Test : {resultat}");
        }

        private void BtnTestVerrouiller_Click(object? sender, RoutedEventArgs e)
        {
            int idCasier = RecupererCasierTest();

            string resultat = lockerController.FermerCasier(idCasier);

            txtCasier.Text = $"Casier n°{idCasier}";
            MettreAJourEtat($"Casier n°{idCasier} verrouillé");

            AjouterLog($"Test : {resultat}");

            if (casierActuel == idCasier)
            {
                casierActuel = -1;
                txtCasier.Text = "Aucun casier sélectionné";
            }
        }

        private int RecupererCasierDepuisCode(Colis colis)
        {
            string codeColis = colis.CodeColis ?? "";

            if (string.IsNullOrWhiteSpace(codeColis))
            {
                AjouterLog("code_colis absent");
                return -1;
            }

            char premierCaractere = codeColis[0];

            if (premierCaractere == '1')
            {
                return 1;
            }

            if (premierCaractere == '2')
            {
                return 2;
            }

            if (premierCaractere == '3')
            {
                return 3;
            }

            if (premierCaractere == '4')
            {
                return 4;
            }

            AjouterLog("Premier chiffre du code invalide");
            return -1;
        }

        private void MettreAJourEtat(string message)
        {
            txtEtatCasier.Text = message;
        }

        private void AjouterLog(string message)
        {
            string ligne = $"[{DateTime.Now:dd/MM/yyyy HH:mm:ss}] {message}";

            lignesLogs.Add(ligne);

            if (lignesLogs.Count > 8)
            {
                lignesLogs.RemoveAt(0);
            }

            txtLogs.Text = string.Join(Environment.NewLine, lignesLogs);
            txtLogs.CaretIndex = txtLogs.Text.Length;
        }
    }
}