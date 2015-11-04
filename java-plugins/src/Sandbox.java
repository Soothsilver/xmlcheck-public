import name.hon2a.asmp.domsax.Main;

/**
 * This class serves no purpose.
 * It is used strictly for debugging purposes if a developer wants to quickly try to run a similarity module function.
 *
 * FOR SOME REASON, the existence of this class is important. DO NOT DELETE OR MESS WITH THIS CLASS.
 * I KNOW THAT DomSaxEntryPoint should work just as well as this BUT IT DOES NOT.
 * Simply replacing "Sandbox" with "DomSaxEntryPoint" in the buildfile will cause the SAX output file to be empty.
 * Don't ask me how.
 *
 */
public class Sandbox {
    /**
     * The developer may test stuff here.
     * @param args Command-line arguments.
     */
    public static void main(String[] args)
    {

        Main.main(args);
        /*
        Main.main(new String[] {
           args[0] // "C:\\Apps\\EasyPHP\\data\\localweb\\xmlcheck\\phptests\\plugins\\cases\\DOMSAX\\domSax_correct.zip"
        } );
        */
        /*
        Main domSax = new Main();
        System.out.println(
                domSax.run(
                        new String[] {
                                "C:\\Apps\\EasyPHP\\data\\localweb\\xmlcheck\\phptests\\plugins\\cases\\DOMSAX\\domSax_correct.zip"
                        } ));*/

    }
}

