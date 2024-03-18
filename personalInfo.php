<?php
require_once('db_conn.php');
session_start();
?>
<style>
    /* Custom CSS styling */
   
</style>
<?php
class PersonalInfo
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function addOrUpdateInfo()
    {
        if (!isset($_SESSION['Id'])) {
            header("Location: home.php");
            exit();
        }

        $Login_Id = $_SESSION['Id'];

        $Fname = $this->cleanInput($_POST['Fname']);
        $Mname = $this->cleanInput($_POST['Mname']);
        $Lname = $this->cleanInput($_POST['Lname']);
        $DOB = $this->cleanInput($_POST['DOB']);
        $Address = $this->cleanInput($_POST['address']);

        $userDetailsCollection = $this->db->userdetails;

        $existingUserDetails = $userDetailsCollection->findOne(['Login_Id' => $Login_Id]);

        if ($existingUserDetails) {
            $userDetailsCollection->updateOne(
                ['Login_Id' => $Login_Id],
                ['$set' => [
                    'F_Name' => $Fname,
                    'M_Name' => $Mname,
                    'L_Name' => $Lname,
                    'DOB' => $DOB,
                    'Address' => $Address
                ]]
            );

            $Msg = "Information updated successfully!";
        } else {
            $userDetailsDocument = [
                'Login_Id' => $Login_Id,
                'F_Name' => $Fname,
                'M_Name' => $Mname,
                'L_Name' => $Lname,
                'DOB' => $DOB,
                'Address' => $Address
            ];

            $userDetailsCollection->insertOne($userDetailsDocument);

            $Msg = "Information added successfully!";
        }

        return $Msg;
    }

    private function cleanInput($input)
    {
        return htmlspecialchars(strip_tags($input));
    }
}

$personalInfo = new PersonalInfo($db);
$Msg = '';

if (isset($_POST['btnAddInfo'])) {
    $Msg = $personalInfo->addOrUpdateInfo();
}

$title = "Add Personal Information";
require_once "./template/header.php";
?>

<div class="container" style="max-width: 600px;" >
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Personal Information</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <?php
                        $userDetailsCollection = $db->userdetails;
                        $Login_Id = $_SESSION['Id'];
                        $existingUserDetails = $userDetailsCollection->findOne(['Login_Id' => $Login_Id]);

                        if ($existingUserDetails) {
                            $arr = [
                                'F_Name' => $existingUserDetails['F_Name'],
                                'M_Name' => $existingUserDetails['M_Name'],
                                'L_Name' => $existingUserDetails['L_Name'],
                                'DOB' => $existingUserDetails['DOB'],
                                'Address' => $existingUserDetails['Address']
                            ];
                        } else {
                            $arr = [];
                        }
                        ?>
                        <div class="form-group">
                            <input type="text" class="form-control" id="Fname" name="Fname" placeholder="Enter First Name" value="<?php echo isset($arr['F_Name']) ? $arr['F_Name'] : ''; ?>" required />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="Mname" name="Mname" placeholder="Enter Middle Name" value="<?php echo isset($arr['M_Name']) ? $arr['M_Name'] : ''; ?>" required />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="Lname" name="Lname" placeholder="Enter Last Name" value="<?php echo isset($arr['L_Name']) ? $arr['L_Name'] : ''; ?>" required />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="DOB" name="DOB" placeholder="Enter Date Of Birth" value="<?php echo isset($arr['DOB']) ? $arr['DOB'] : ''; ?>" required />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter Address" value="<?php echo isset($arr['Address']) ? $arr['Address'] : ''; ?>" required />
                        </div>
                        <button class="btn btn-primary btn-block" type="submit" id="btnAddInfo" name="btnAddInfo"><?php echo $existingUserDetails ? 'Update Information' : 'Add Information'; ?></button>
                    </form>
                    <?php if ($Msg) : ?>
                        <div class="alert alert-success mt-3" role="alert">
                            <?php echo $Msg; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "./template/footer.php";
?>
