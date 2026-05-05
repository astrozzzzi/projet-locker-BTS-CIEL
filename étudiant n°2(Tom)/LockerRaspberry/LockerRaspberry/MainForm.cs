using static System.Runtime.CompilerServices.RuntimeHelpers;
using System;
using System.Windows.Forms;
using LockerControllerApp.Services;


namespace LockerRaspberry
{
    public partial class MainForm : Form
    {
        private LockerController lockerController;
        private ApiService apiService = new ApiService();


        public MainForm()
        {
            InitializeComponent();

            lockerController = new LockerController();
            apiService = new ApiService();

            lockerController.OnLog += AjouterLog;
        }

        private async void btnValiderCode_Click(object sender, EventArgs e)
        {
            string code = txtCode.Text;

            richTextBoxLogs.AppendText($"Code saisi/scanné : {code}\n");

            bool colisExiste = await apiService.VerifierColisAsync(code);

            if (colisExiste)
            {
                richTextBoxLogs.AppendText("Colis trouvé dans la base.\n");
                richTextBoxLogs.AppendText("Ouverture du casier...\n");

                // Plus tard : appel au LockerController
                // lockerController.OuvrirCasier(1);
            }
            else
            {
                richTextBoxLogs.AppendText("Colis introuvable ou erreur API.\n");
            }
        }

        private void btnFermerCasier_Click(object sender, EventArgs e)
        {
            lockerController.FermerCasier(1);
        }

        private void AjouterLog(string message)
        {
            richTextBoxLogs.AppendText($"[{DateTime.Now:HH:mm:ss}] {message}\n");
        }
    }
}
