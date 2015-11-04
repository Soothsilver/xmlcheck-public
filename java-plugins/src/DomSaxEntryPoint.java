import name.hon2a.asmp.domsax.Main;

/**
 * This class serves as an entry point for the DomSaxPlugin plugin.
 * For some reason unknown to me, if I choose name.hon2a.asp.domsax.Main as the main class in the manifest,
 * the file output for SAX will not be generated.
 */
public class DomSaxEntryPoint {
    /**
     * The entry point.
     * @param args Command-line arguments.
     */
    public static void main(String[] args)
    {

        Main.main(args);

    }
}

