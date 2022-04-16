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
		if((len_first * amt_first + len_second * amt_second) < len_target) {
			System.out.println(false);
			return;
		}
		for(int i = 0; i <= amt_first; i++) {
			if((i * len_first + amt_second * len_second) < len_target
			||	len_target - i * len_first < 0) {
				continue;
			}
			if((len_target - i * len_first) % len_second == 0) {
				System.out.println(true);
				return;
			}
		}
		System.out.println(false);
	}
	
}