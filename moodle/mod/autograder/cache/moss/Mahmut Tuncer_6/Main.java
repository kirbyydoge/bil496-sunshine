public class Main {
	
	public static void main(String[] args) {
		if(args.length != 5) {
			System.exit(0);
		}
		int len_first = Integer.parseInt(args[0]);
		int amt_first = Integer.parseInt(args[1]);
		int len_second = Integer.parseInt(args[2]);
		int amt_second = Integer.parseInt(args[3]);
		int len_target = Integer.parseInt(args[4]);
		int buildable = false;
		for(int i = 0; i < amt_first; i++) {
			for(int j = 0; j < amt_second; j++) {
				if((i * len_first + j * len_second) == len_target) {
					System.out.println(true);
					buildable = true;
				}
			}
		}
		if (!buildable) {
			System.out.println(false);
		}
	}
	
}