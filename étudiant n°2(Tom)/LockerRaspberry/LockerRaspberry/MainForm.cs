using System;
using System.Windows.Forms;
using LockerRaspberry.Services;
using LockerRaspberry.Controllers;

namespace LockerRaspberry
{
    public partial class MainForm : Form
    {
        private readonly ApiService apiService;
        private readonly LockerController lockerController;

        private int casierActuel = -1;

        public MainForm()
        {
            InitializeComponent();

            apiService = new ApiService();
            lockerController = new LockerController();

            AjouterLog("Application Locker Raspberry démarrée.");
        }

        private async void btnDeposerColis_Click(object sender, EventArgs e)
        {
            string numeroColis = txtCode.Text.Trim();

            if (numeroColis == "")
            {
                AjouterLog("Veuillez saisir un numéro de colis.");
                return;
            }

            AjouterLog($"Recherche du colis {numeroColis}...");

            string reponse = await apiService.TrackColisAsync(numeroColis);

            if (string.IsNullOrWhiteSpace(reponse))
            {
                AjouterLog("Erreur API.");
                return;
            }

            AjouterLog("Colis trouvé.");

            // TEMPORAIRE
            // À remplacer plus tard par la lecture du vrai Casier_idCasier
            casierActuel = 1;

            lockerController.OuvrirCasier(casierActuel);

            AjouterLog($"Casier {casierActuel} ouvert pour dépôt.");
        }

        private async void btnRetirerColis_Click(object sender, EventArgs e)
        {
            string numeroColis = txtCode.Text.Trim();

            if (numeroColis == "")
            {
                AjouterLog("Veuillez saisir un numéro de colis.");
                return;
            }

            AjouterLog($"Recherche du colis {numeroColis}...");

            string reponse = await apiService.TrackColisAsync(numeroColis);

            if (string.IsNullOrWhiteSpace(reponse))
            {
                AjouterLog("Erreur API.");
                return;
            }

            AjouterLog("Colis trouvé.");

            // TEMPORAIRE
            casierActuel = 1;

            lockerController.OuvrirCasier(casierActuel);

            AjouterLog($"Casier {casierActuel} ouvert pour retrait.");
        }

        private void btnFermerCasier_Click(object sender, EventArgs e)
        {
            if (casierActuel == -1)
            {
                AjouterLog("Aucun casier ouvert.");
                return;
            }

            lockerController.FermerCasier(casierActuel);

            AjouterLog($"Casier {casierActuel} fermé.");

            casierActuel = -1;
        }

        private void AjouterLog(string message)
        {
            richTextBoxLogs.AppendText(
                $"[{DateTime.Now:HH:mm:ss}] {message}\n");
        }
    }
}